<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\QuizAccess;
use App\Http\Requests\VerifyPaymentRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
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
     * Initiate a UPI payment
     */
    public function initiate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'type' => 'nullable|string|in:payment,tokens',
            'description' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();
        $orderId = 'MM-' . strtoupper(Str::random(8)) . '-' . time();
        
        $type = $request->input('type', 'payment');
        $description = $request->input('description', 'MetrixsMate Assessment Payment');

        $payment = Payment::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'amount' => $request->amount,
            'currency' => 'INR',
            'status' => 'pending',
            'payment_method' => 'upi',
            'description' => $description,
            'metadata' => [
                'type' => $type,
                'created_at' => now()->toIso8601String(),
            ],
        ]);

        // Generate UPI deep link
        $upiId = config('services.upi.merchant_id', 'metrixsmate@upi');
        $merchantName = config('services.upi.merchant_name', 'MetrixsMate');
        $upiLink = $this->generateUpiLink($upiId, $merchantName, $payment->amount, $orderId);

        return view('payments.checkout', compact('payment', 'upiLink', 'upiId', 'merchantName'));
    }

    /**
     * Verify payment (manual confirmation for free UPI)
     */
    public function verify(VerifyPaymentRequest $request, Payment $payment)
    {
        if ($payment->isCompleted()) {
            return redirect()->route('payments.index')
                ->with('error', 'This payment is already verified.');
        }

        $payment->update([
            'transaction_id' => $request->transaction_id,
            'upi_id' => $request->upi_id,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $user = Auth::user();

        // Handle different payment types
        $paymentType = $payment->metadata['type'] ?? 'payment';
        
        if ($paymentType === 'tokens') {
            // Add search tokens for token purchase
            if ($payment->amount >= 100) {
                $user->addSearchTokens(3); // 3 tokens for ₹100
                return redirect()->route('school-finder.ai.index')
                    ->with('success', '🎁 Payment verified! 3 search tokens added to your account. Happy searching!');
            }
        } else {
            // Grant access to all quizzes for regular assessment payment
            $quizzes = Quiz::all();
            
            foreach ($quizzes as $quiz) {
                QuizAccess::grantViaPayment($user, $quiz, $payment);
            }

            return redirect()->route('payments.index')
                ->with('success', 'Payment verified successfully! You now have access to all assessments. Thank you.');
        }

        return redirect()->route('payments.index')
            ->with('success', 'Payment verified successfully!');
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
     * Generate UPI payment link
     */
    private function generateUpiLink(string $upiId, string $name, float $amount, string $orderId): string
    {
        $params = http_build_query([
            'pa' => $upiId,
            'pn' => $name,
            'am' => number_format($amount, 2, '.', ''),
            'cu' => 'INR',
            'tn' => 'MetrixsMate Payment - ' . $orderId,
            'tr' => $orderId,
        ]);

        return "upi://pay?{$params}";
    }
}
