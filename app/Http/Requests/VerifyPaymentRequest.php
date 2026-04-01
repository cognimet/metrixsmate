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
        $payment = Payment::find($this->route('payment'));
        return $payment && $payment->user_id === auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $paymentId = $this->route('payment');
        
        return [
            'transaction_id' => [
                'required',
                'string',
                'min:6',
                'max:50',
                'regex:/^[a-zA-Z0-9]{6,50}$/',
                Rule::unique('payments', 'transaction_id')
                    ->ignore($paymentId),
            ],
            'upi_id' => 'nullable|string|max:100|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z]{3,}$/',
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
            'transaction_id.max' => 'Transaction ID cannot exceed 50 characters.',
            'transaction_id.regex' => 'Transaction ID must contain only alphanumeric characters (a-z, A-Z, 0-9).',
            'transaction_id.unique' => 'This transaction ID has already been used for another payment. Please verify the correct transaction ID.',
            'upi_id.regex' => 'UPI ID format is invalid. Example: yourname@upi',
        ];
    }
}
