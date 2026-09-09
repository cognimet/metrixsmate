<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Payment;

class VerifyPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $routePayment = $this->route('payment');

        if ($routePayment instanceof Payment) {
            return $routePayment->user_id === auth()->id();
        }

        $payment = Payment::find($routePayment);

        return $payment && $payment->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $routePayment = $this->route('payment');
        $paymentId = $routePayment instanceof Payment ? $routePayment->id : $routePayment;
        
        return [
            'transaction_id' => [
                'required',
                'string',
                'min:6',
                'max:100',
                'regex:/^[a-zA-Z0-9._-]{6,100}$/',
                Rule::unique('payments', 'transaction_id')
                    ->ignore($paymentId),
            ],
            'razorpay_order_id' => 'nullable|string|max:120',
            'razorpay_payment_id' => 'nullable|string|max:120',
            'razorpay_signature' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'transaction_id.required' => 'Transaction ID is required.',
            'transaction_id.min' => 'Transaction ID must be at least 6 characters.',
            'transaction_id.max' => 'Transaction ID cannot exceed 100 characters.',
            'transaction_id.regex' => 'Transaction ID may only include letters, numbers, dots, underscores, and hyphens.',
            'transaction_id.unique' => 'This transaction ID has already been used for another payment. Please verify the correct transaction ID.',
        ];
    }
}
