<?php

namespace App\Http\Requests\Frontend\Rides;

use Illuminate\Foundation\Http\FormRequest;

class FinishRideRequest extends FormRequest
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
            'lat' => 'required|decimal:0,14',
            'long' => 'required|decimal:0,14',
        ];
    }
}
