<?php

namespace App\Http\Requests\Frontend\Payments;

use Illuminate\Foundation\Http\FormRequest;

class CreatePaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'number' => 'required|integer|max_digits:16',
            'cvc' => 'required|string|max:3',
            'exp_month' => 'required|integer|max_digits:2',
            'exp_year' => 'required|integer|max_digits:4',
            'card_holder' => 'required|string|max:255',
        ];
    }
}
