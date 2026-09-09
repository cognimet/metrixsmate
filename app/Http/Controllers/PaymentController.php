<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAccess;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class PaymentController extends Controller
{
    private const ASSESSMENT_PRICE_USD = 15.00;

    /**
     * Show the payment page
     */
    public function index()
    {
        $user = Auth::user();
        $payments = Payment::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get();

        // Check if user has any completed payment
        $hasPaid = Payment::where('user_id', $user->id)
            ->where('status', 'completed')
            ->exists();

        return view('payments.index', compact('payments', 'hasPaid'));
    }

    /**
     * Initiate a payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount'      => 'nullable|numeric|min:0',
            'coupon_code' => 'nullable|string|max:50',
            'type'        => 'nullable|string|in:payment,tokens',
            'description' => 'nullable|string|max:255',
            'payment_method' => 'nullable|string|in:paypal,razorpay',
        ]);

        $user        = Auth::user();
        $orderId     = 'MM-' . strtoupper(Str::random(8)) . '-' . time();
        $type        = $request->input('type', 'payment');
        $description = $request->input('description', 'MetrixsMate Assessment Payment');
        $paymentMethod = $request->input('payment_method', $type === 'payment' ? 'paypal' : 'razorpay');

        $currency       = $type === 'payment'
            ? strtoupper((string) config('services.paypal.currency', 'USD'))
            : 'INR';
        $amount         = $type === 'payment'
            ? self::ASSESSMENT_PRICE_USD
            : (float) $request->input('amount', 0);
        $originalAmount = $amount;
        $couponId       = null;
        $discountAmount = 0;
        $coupon         = null;

        // Apply coupon discount if provided
        if ($request->filled('coupon_code')) {
            $coupon = Coupon::where('code', strtoupper($request->coupon_code))->first();

            if ($coupon && $coupon->isValid()) {
                if ($coupon->discount_type === 'percentage') {
                    $discountAmount = round($originalAmount * $coupon->discount_value / 100, 2);
                } else {
                    $discountAmount = min((float) $coupon->discount_value, $originalAmount);
                }
                $amount   = round($originalAmount - $discountAmount, 2);
                $couponId = $coupon->id;
            }
        }

        // If the coupon brings the price to zero, grant access immediately
        if ($amount <= 0 && $type === 'payment') {
            $quizzes = Quiz::all();
            foreach ($quizzes as $quiz) {
                QuizAccess::firstOrCreate(
                    ['user_id' => $user->id, 'quiz_id' => $quiz->id],
                    ['coupon_id' => $couponId, 'access_type' => 'coupon', 'granted_at' => now()]
                );
            }

            if ($coupon) {
                $coupon->use($user);
            }

            return redirect()->route('dashboard')
                ->with('success', 'Your coupon covers the full amount! You now have access to all assessments.');
        }

        $payment = Payment::create([
            'user_id'        => $user->id,
            'order_id'       => $orderId,
            'amount'         => $amount,
            'currency'       => $currency,
            'status'         => 'pending',
            'payment_method' => $paymentMethod,
            'description'    => $description,
            'metadata'       => [
                'type'            => $type,
                'gateway'         => $paymentMethod,
                'coupon_id'       => $couponId,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'created_at'      => now()->toIso8601String(),
            ],
        ]);

        $gatewayData = $this->buildGatewayData($payment);

        return view('payments.checkout', compact('payment', 'gatewayData'));
    }

    /**
     * Complete Razorpay payment after checkout callback
     */
    public function completeRazorpay(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        if ($payment->isCompleted()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('payments.index'),
                'message' => 'Payment already completed.',
            ]);
        }

        if ($payment->payment_method !== 'razorpay') {
            return response()->json([
                'success' => false,
                'message' => 'This payment was not started with Razorpay.',
            ], 422);
        }

        $payload = $request->validate([
            'razorpay_order_id' => 'required|string|max:120',
            'razorpay_payment_id' => 'required|string|max:120',
            'razorpay_signature' => 'required|string|max:255',
        ]);

        $expectedOrderId = (string) ($payment->metadata['razorpay_order_id'] ?? '');
        if ($expectedOrderId === '' || $expectedOrderId !== (string) $payload['razorpay_order_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay order mismatch. Please retry from checkout.',
            ], 422);
        }

        $isValidSignature = $this->isValidRazorpaySignature(
            (string) $payload['razorpay_order_id'],
            (string) $payload['razorpay_payment_id'],
            (string) $payload['razorpay_signature']
        );

        if (! $isValidSignature) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay signature verification failed.',
            ], 422);
        }

        $razorpayPayment = $this->fetchRazorpayPayment((string) $payload['razorpay_payment_id']);
        if (! $razorpayPayment) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to confirm payment status with Razorpay.',
            ], 422);
        }

        if (strtolower((string) ($razorpayPayment['status'] ?? '')) !== 'captured') {
            return response()->json([
                'success' => false,
                'message' => 'Payment was not captured by Razorpay.',
            ], 422);
        }

        if ((string) ($razorpayPayment['order_id'] ?? '') !== $expectedOrderId) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay payment order does not match.',
            ], 422);
        }

        $expectedAmount = (int) round(((float) $payment->amount) * 100);
        if ((int) ($razorpayPayment['amount'] ?? -1) !== $expectedAmount) {
            return response()->json([
                'success' => false,
                'message' => 'Razorpay amount validation failed.',
            ], 422);
        }

        $this->markPaymentCompleted($payment, (string) $payload['razorpay_payment_id'], [
            'razorpay_order_id' => (string) $payload['razorpay_order_id'],
            'razorpay_payment_id' => (string) $payload['razorpay_payment_id'],
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('payments.index'),
            'message' => $this->completionMessage($payment),
        ]);
    }

    /**
     * Capture PayPal order and complete payment
     */
    public function capturePayPal(Request $request, Payment $payment)
    {
        abort_unless($payment->user_id === Auth::id(), 403);

        if ($payment->isCompleted()) {
            return response()->json([
                'success' => true,
                'redirect_url' => route('payments.index'),
                'message' => 'Payment already completed.',
            ]);
        }

        if ($payment->payment_method !== 'paypal') {
            return response()->json([
                'success' => false,
                'message' => 'This payment was not started with PayPal.',
            ], 422);
        }

        $payload = $request->validate([
            'paypal_order_id' => 'required|string|max:120',
        ]);

        $expectedOrderId = (string) ($payment->metadata['paypal_order_id'] ?? '');
        if ($expectedOrderId === '' || $expectedOrderId !== (string) $payload['paypal_order_id']) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal order mismatch. Please retry checkout.',
            ], 422);
        }

        $captureResult = $this->capturePayPalOrder((string) $payload['paypal_order_id']);
        $captureResponse = $captureResult['data'] ?? null;

        if (! $captureResponse) {
            $fallbackOrder = $this->getPayPalOrder((string) $payload['paypal_order_id']);
            $captureResponse = $this->extractPayPalCaptureFromOrder($fallbackOrder);
        }

        if (! $captureResponse) {
            return response()->json([
                'success' => false,
                'message' => $captureResult['error_message'] ?? 'Unable to capture payment from PayPal. Please retry once.',
            ], 422);
        }

        $captureId = (string) ($captureResponse['purchase_units'][0]['payments']['captures'][0]['id'] ?? '');
        $captureStatus = strtoupper((string) ($captureResponse['purchase_units'][0]['payments']['captures'][0]['status'] ?? ''));
        $captureAmount = (string) ($captureResponse['purchase_units'][0]['payments']['captures'][0]['amount']['value'] ?? '');
        $captureCurrency = strtoupper((string) ($captureResponse['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'] ?? ''));

        if ($captureId === '' || $captureStatus !== 'COMPLETED') {
            return response()->json([
                'success' => false,
                'message' => 'PayPal capture is not completed.',
            ], 422);
        }

        if ($captureCurrency !== strtoupper((string) $payment->currency)) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal currency validation failed.',
            ], 422);
        }

        if (abs((float) $captureAmount - (float) $payment->amount) > 0.01) {
            return response()->json([
                'success' => false,
                'message' => 'PayPal amount validation failed.',
            ], 422);
        }

        $this->markPaymentCompleted($payment, $captureId, [
            'paypal_order_id' => (string) $payload['paypal_order_id'],
            'paypal_capture_id' => $captureId,
        ]);

        return response()->json([
            'success' => true,
            'redirect_url' => route('payments.index'),
            'message' => $this->completionMessage($payment),
        ]);
    }

    /**
     * Razorpay webhook callback
     */
    public function handleRazorpayWebhook(Request $request)
    {
        $rawPayload = (string) $request->getContent();
        $signature = $request->header('X-Razorpay-Signature');

        if (! $this->isValidRazorpayWebhookSignature($rawPayload, is_string($signature) ? $signature : null)) {
            return response()->json(['success' => false, 'message' => 'Invalid webhook signature.'], 401);
        }

        $event = $request->json()->all();
        $eventType = (string) ($event['event'] ?? '');

        if (! in_array($eventType, ['payment.captured', 'order.paid'], true)) {
            return response()->json(['success' => true, 'message' => 'Event ignored.']);
        }

        $entity = $event['payload']['payment']['entity'] ?? [];
        $razorpayOrderId = (string) ($entity['order_id'] ?? '');
        $razorpayPaymentId = (string) ($entity['id'] ?? '');

        if ($razorpayOrderId === '' || $razorpayPaymentId === '') {
            return response()->json(['success' => true, 'message' => 'No actionable payment payload.']);
        }

        $payment = $this->findPaymentByGatewayOrder('razorpay', $razorpayOrderId);
        if (! $payment) {
            return response()->json(['success' => true, 'message' => 'No matching payment found.']);
        }

        if ($payment->isCompleted()) {
            return response()->json(['success' => true, 'message' => 'Payment already completed.']);
        }

        $amount = (int) ($entity['amount'] ?? -1);
        $expectedAmount = (int) round(((float) $payment->amount) * 100);
        if ($amount !== $expectedAmount) {
            return response()->json(['success' => false, 'message' => 'Amount mismatch.'], 422);
        }

        $status = strtolower((string) ($entity['status'] ?? ''));
        if ($status !== 'captured') {
            return response()->json(['success' => true, 'message' => 'Payment not captured yet.']);
        }

        $this->markPaymentCompleted($payment, $razorpayPaymentId, [
            'razorpay_order_id' => $razorpayOrderId,
            'razorpay_payment_id' => $razorpayPaymentId,
            'completed_via' => 'razorpay_webhook',
        ]);

        return response()->json(['success' => true, 'message' => 'Webhook processed.']);
    }

    /**
     * PayPal webhook callback
     */
    public function handlePayPalWebhook(Request $request)
    {
        $event = $request->json()->all();
        if (! is_array($event) || empty($event)) {
            return response()->json(['success' => false, 'message' => 'Invalid webhook payload.'], 422);
        }

        if (! $this->isValidPayPalWebhookSignature($request, $event)) {
            return response()->json(['success' => false, 'message' => 'Invalid webhook signature.'], 401);
        }

        $eventType = (string) ($event['event_type'] ?? '');
        if ($eventType !== 'PAYMENT.CAPTURE.COMPLETED') {
            return response()->json(['success' => true, 'message' => 'Event ignored.']);
        }

        $resource = $event['resource'] ?? [];
        $captureId = (string) ($resource['id'] ?? '');
        $paypalOrderId = (string) ($resource['supplementary_data']['related_ids']['order_id'] ?? '');

        if ($captureId === '' || $paypalOrderId === '') {
            return response()->json(['success' => true, 'message' => 'No actionable capture payload.']);
        }

        $payment = $this->findPaymentByGatewayOrder('paypal', $paypalOrderId);
        if (! $payment) {
            return response()->json(['success' => true, 'message' => 'No matching payment found.']);
        }

        if ($payment->isCompleted()) {
            return response()->json(['success' => true, 'message' => 'Payment already completed.']);
        }

        $amount = (float) ($resource['amount']['value'] ?? 0);
        $currency = strtoupper((string) ($resource['amount']['currency_code'] ?? ''));

        if (abs($amount - (float) $payment->amount) > 0.01 || $currency !== strtoupper((string) $payment->currency)) {
            return response()->json(['success' => false, 'message' => 'Amount or currency mismatch.'], 422);
        }

        $this->markPaymentCompleted($payment, $captureId, [
            'paypal_order_id' => $paypalOrderId,
            'paypal_capture_id' => $captureId,
            'completed_via' => 'paypal_webhook',
        ]);

        return response()->json(['success' => true, 'message' => 'Webhook processed.']);
    }

    /**
     * Payment history
     */
    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('payments.history', compact('payments'));
    }

    /**
     * Mark payment completed and grant entitlement
     */
    private function markPaymentCompleted(Payment $payment, string $transactionId, array $metadataUpdates = []): void
    {
        $metadata = $payment->metadata ?? [];
        foreach ($metadataUpdates as $key => $value) {
            $metadata[$key] = $value;
        }

        $payment->update([
            'transaction_id' => $transactionId,
            'status' => 'completed',
            'paid_at' => now(),
            'metadata' => $metadata,
        ]);

        $this->grantEntitlement($payment);
    }

    /**
     * Grant feature access after successful payment
     */
    private function grantEntitlement(Payment $payment): void
    {
        $user = $payment->user;
        if (! $user) {
            return;
        }

        $paymentType = $payment->metadata['type'] ?? 'payment';

        if ($paymentType === 'tokens') {
            if ($payment->amount >= 100) {
                $user->addSearchTokens(3);
            }
            return;
        }

        $quizzes = Quiz::all();
        foreach ($quizzes as $quiz) {
            QuizAccess::grantViaPayment($user, $quiz, $payment);
        }

        if ($couponId = $payment->metadata['coupon_id'] ?? null) {
            $coupon = Coupon::find($couponId);
            if ($coupon) {
                $coupon->use($user);
            }
        }
    }

    /**
     * Completion flash message by payment type
     */
    private function completionMessage(Payment $payment): string
    {
        $paymentType = $payment->metadata['type'] ?? 'payment';

        if ($paymentType === 'tokens') {
            return 'Payment completed successfully!';
        }

        return 'Payment verified successfully! You now have access to all assessments.';
    }

    /**
     * Build provider-specific checkout payload
     */
    private function buildGatewayData(Payment $payment): array
    {
        if ($payment->payment_method === 'paypal') {
            $paypalOrder = $this->createPayPalOrder($payment);

            if (! $paypalOrder) {
                return [
                    'method' => 'paypal',
                    'is_configured' => false,
                    'message' => 'PayPal is not configured. Set PAYPAL_CLIENT_ID and PAYPAL_CLIENT_SECRET to continue.',
                ];
            }

            $metadata = $payment->metadata ?? [];
            $metadata['paypal_order_id'] = $paypalOrder['id'] ?? null;
            $payment->update(['metadata' => $metadata]);

            return [
                'method' => 'paypal',
                'is_configured' => true,
                'client_id' => config('services.paypal.client_id'),
                'currency' => strtoupper((string) $payment->currency),
                'paypal_order_id' => $paypalOrder['id'] ?? null,
            ];
        }

        $razorpayOrder = $this->createRazorpayOrder($payment);

        if (! $razorpayOrder) {
            return [
                'method' => 'razorpay',
                'is_configured' => false,
                'message' => 'Razorpay is not configured. Set RAZORPAY_KEY_ID and RAZORPAY_KEY_SECRET to continue.',
            ];
        }

        $metadata = $payment->metadata ?? [];
        $metadata['razorpay_order_id'] = $razorpayOrder['id'] ?? null;
        $payment->update(['metadata' => $metadata]);

        return [
            'method' => 'razorpay',
            'is_configured' => true,
            'key_id' => config('services.razorpay.key_id'),
            'razorpay_order_id' => $razorpayOrder['id'] ?? null,
            'amount' => $razorpayOrder['amount'] ?? null,
            'currency' => $razorpayOrder['currency'] ?? strtoupper((string) $payment->currency),
            'name' => config('app.name', 'MetrixsMate'),
            'description' => $payment->description ?: 'MetrixsMate Payment',
            'prefill' => [
                'name' => Auth::user()?->name,
                'email' => Auth::user()?->email,
            ],
        ];
    }

    /**
     * Create PayPal order for checkout
     */
    private function createPayPalOrder(Payment $payment): ?array
    {
        $accessToken = $this->getPayPalAccessToken();
        if (! $accessToken) {
            return null;
        }

        $amount = number_format((float) $payment->amount, 2, '.', '');
        $currency = strtoupper((string) $payment->currency);
        $baseUrl = $this->payPalApiBaseUrl();

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($accessToken)
                ->withHeaders(['PayPal-Request-Id' => 'MM-PAYPAL-' . $payment->order_id])
                ->post($baseUrl . '/v2/checkout/orders', [
                    'intent' => 'CAPTURE',
                    'purchase_units' => [[
                        'reference_id' => (string) $payment->id,
                        'custom_id' => (string) $payment->id,
                        'invoice_id' => $payment->order_id,
                        'description' => $payment->description ?: 'MetrixsMate Payment',
                        'amount' => [
                            'currency_code' => $currency,
                            'value' => $amount,
                        ],
                    ]],
                    'application_context' => [
                        'brand_name' => config('app.name', 'MetrixsMate'),
                        'user_action' => 'PAY_NOW',
                        'return_url' => config('services.paypal.return_url', route('payments.index')),
                        'cancel_url' => config('services.paypal.cancel_url', route('payments.index')),
                    ],
                ]);

            if (! $response->successful()) {
                Log::warning('PayPal order creation failed.', [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return null;
            }

            $payload = $response->json();
            if (! is_array($payload) || empty($payload['id'])) {
                return null;
            }

            return $payload;
        } catch (Throwable $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Capture PayPal order
     */
    private function capturePayPalOrder(string $paypalOrderId): array
    {
        $accessToken = $this->getPayPalAccessToken();
        if (! $accessToken) {
            return [
                'data' => null,
                'error_message' => 'PayPal credentials are missing. Check PAYPAL_CLIENT_ID and PAYPAL_CLIENT_SECRET.',
            ];
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($accessToken)
                // PayPal capture endpoint expects a valid JSON body; send {} explicitly.
                ->withBody('{}', 'application/json')
                ->post($this->payPalApiBaseUrl() . '/v2/checkout/orders/' . urlencode($paypalOrderId) . '/capture');

            if (! $response->successful()) {
                $payload = $response->json();
                $errorMessage = $this->paypalApiErrorMessage($payload);

                Log::warning('PayPal order capture failed.', [
                    'paypal_order_id' => $paypalOrderId,
                    'http_status' => $response->status(),
                    'response' => $payload,
                ]);

                return [
                    'data' => null,
                    'error_message' => $errorMessage,
                ];
            }

            $payload = $response->json();

            return [
                'data' => is_array($payload) ? $payload : null,
                'error_message' => null,
            ];
        } catch (Throwable $exception) {
            report($exception);
            return [
                'data' => null,
                'error_message' => 'Unable to connect to PayPal capture API. Please try again.',
            ];
        }
    }

    /**
     * Fetch PayPal order details
     */
    private function getPayPalOrder(string $paypalOrderId): ?array
    {
        $accessToken = $this->getPayPalAccessToken();
        if (! $accessToken) {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($accessToken)
                ->get($this->payPalApiBaseUrl() . '/v2/checkout/orders/' . urlencode($paypalOrderId));

            if (! $response->successful()) {
                Log::warning('PayPal order fetch failed.', [
                    'paypal_order_id' => $paypalOrderId,
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return null;
            }

            $payload = $response->json();

            return is_array($payload) ? $payload : null;
        } catch (Throwable $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Extract completed capture payload from PayPal order response
     */
    private function extractPayPalCaptureFromOrder(?array $orderPayload): ?array
    {
        if (! is_array($orderPayload)) {
            return null;
        }

        $captures = $orderPayload['purchase_units'][0]['payments']['captures'] ?? null;
        if (! is_array($captures) || empty($captures[0])) {
            return null;
        }

        $capture = $captures[0];
        if (strtoupper((string) ($capture['status'] ?? '')) !== 'COMPLETED') {
            return null;
        }

        return $orderPayload;
    }

    /**
     * Build user-safe PayPal API error message
     */
    private function paypalApiErrorMessage(mixed $payload): string
    {
        if (! is_array($payload)) {
            return 'Unable to capture payment from PayPal. Please retry once.';
        }

        $issue = strtoupper((string) ($payload['details'][0]['issue'] ?? ''));
        $description = (string) ($payload['details'][0]['description'] ?? '');

        return match ($issue) {
            'ORDER_ALREADY_CAPTURED' => 'This PayPal order is already captured. Please refresh and check your payment history.',
            'ORDER_NOT_APPROVED' => 'PayPal order is not approved yet. Please approve the payment and try again.',
            'INVALID_RESOURCE_ID' => 'Invalid PayPal order reference. Please retry checkout from the payment page.',
            default => $description !== ''
                ? $description
                : 'Unable to capture payment from PayPal. Please retry once.',
        };
    }

    /**
     * Retrieve PayPal access token
     */
    private function getPayPalAccessToken(): ?string
    {
        $clientId = config('services.paypal.client_id');
        $clientSecret = config('services.paypal.client_secret');

        if (! $clientId || ! $clientSecret) {
            return null;
        }

        try {
            $response = Http::timeout(20)
                ->asForm()
                ->acceptJson()
                ->withBasicAuth($clientId, $clientSecret)
                ->post($this->payPalApiBaseUrl() . '/v1/oauth2/token', [
                    'grant_type' => 'client_credentials',
                ]);

            if (! $response->successful()) {
                Log::warning('PayPal access token request failed.', [
                    'http_status' => $response->status(),
                    'response' => $response->json(),
                ]);
                return null;
            }

            $payload = $response->json();

            return is_array($payload) ? ($payload['access_token'] ?? null) : null;
        } catch (Throwable $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * PayPal API base URL by mode
     */
    private function payPalApiBaseUrl(): string
    {
        return config('services.paypal.mode', 'sandbox') === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';
    }

    /**
     * Validate Razorpay webhook signature
     */
    private function isValidRazorpayWebhookSignature(string $payload, ?string $signature): bool
    {
        $secret = config('services.razorpay.webhook_secret');

        if (! $secret || ! $signature) {
            return false;
        }

        $generated = hash_hmac('sha256', $payload, (string) $secret);

        return hash_equals($generated, $signature);
    }

    /**
     * Validate PayPal webhook signature via PayPal API
     */
    private function isValidPayPalWebhookSignature(Request $request, array $event): bool
    {
        $webhookId = config('services.paypal.webhook_id');
        $accessToken = $this->getPayPalAccessToken();

        if (! $webhookId || ! $accessToken) {
            return false;
        }

        $verificationPayload = [
            'transmission_id' => (string) $request->header('PAYPAL-TRANSMISSION-ID', ''),
            'transmission_time' => (string) $request->header('PAYPAL-TRANSMISSION-TIME', ''),
            'cert_url' => (string) $request->header('PAYPAL-CERT-URL', ''),
            'auth_algo' => (string) $request->header('PAYPAL-AUTH-ALGO', ''),
            'transmission_sig' => (string) $request->header('PAYPAL-TRANSMISSION-SIG', ''),
            'webhook_id' => (string) $webhookId,
            'webhook_event' => $event,
        ];

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->withToken($accessToken)
                ->post($this->payPalApiBaseUrl() . '/v1/notifications/verify-webhook-signature', $verificationPayload);

            if (! $response->successful()) {
                return false;
            }

            $payload = $response->json();
            $status = strtoupper((string) ($payload['verification_status'] ?? ''));

            return $status === 'SUCCESS';
        } catch (Throwable $exception) {
            report($exception);
            return false;
        }
    }

    /**
     * Find a pending payment by gateway order id in metadata
     */
    private function findPaymentByGatewayOrder(string $gateway, string $gatewayOrderId): ?Payment
    {
        $pendingPayments = Payment::where('payment_method', $gateway)
            ->whereIn('status', ['pending', 'failed'])
            ->get();

        $metadataKey = $gateway === 'paypal' ? 'paypal_order_id' : 'razorpay_order_id';

        return $pendingPayments->first(function (Payment $payment) use ($metadataKey, $gatewayOrderId) {
            return (string) ($payment->metadata[$metadataKey] ?? '') === $gatewayOrderId;
        });
    }

    /**
     * Create Razorpay order for checkout
     */
    private function createRazorpayOrder(Payment $payment): ?array
    {
        $keyId = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');

        if (! $keyId || ! $keySecret) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->withBasicAuth($keyId, $keySecret)
                ->post('https://api.razorpay.com/v1/orders', [
                    'amount' => (int) round(((float) $payment->amount) * 100),
                    'currency' => strtoupper((string) $payment->currency),
                    'receipt' => $payment->order_id,
                    'notes' => [
                        'payment_id' => (string) $payment->id,
                        'user_id' => (string) $payment->user_id,
                    ],
                ]);

            if (! $response->successful()) {
                return null;
            }

            $payload = $response->json();

            if (! is_array($payload) || empty($payload['id'])) {
                return null;
            }

            return $payload;
        } catch (Throwable $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Fetch Razorpay payment details
     */
    private function fetchRazorpayPayment(string $paymentId): ?array
    {
        $keyId = config('services.razorpay.key_id');
        $keySecret = config('services.razorpay.key_secret');

        if (! $keyId || ! $keySecret) {
            return null;
        }

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->withBasicAuth($keyId, $keySecret)
                ->get('https://api.razorpay.com/v1/payments/' . urlencode($paymentId));

            if (! $response->successful()) {
                return null;
            }

            $payload = $response->json();

            return is_array($payload) ? $payload : null;
        } catch (Throwable $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * Verify Razorpay payment signature
     */
    private function isValidRazorpaySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $secret = config('services.razorpay.key_secret');
        if (! $secret) {
            return false;
        }

        $generatedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, $secret);

        return hash_equals($generatedSignature, $signature);
    }

    /*
     * Legacy UPI deep-link flow intentionally disabled during migration
     * to PayPal and Razorpay.
     */
    // private function generateUpiLink(string $upiId, string $name, float $amount, string $orderId): string
    // {
    //     $params = http_build_query([
    //         'pa' => $upiId,
    //         'pn' => $name,
    //         'am' => number_format($amount, 2, '.', ''),
    //         'cu' => 'INR',
    //         'tn' => 'MetrixsMate Payment - ' . $orderId,
    //         'tr' => $orderId,
    //     ]);

    //     return "upi://pay?{$params}";
    // }
}
