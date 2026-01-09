<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
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
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email_primary' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('people')->where(function ($query) {
                    return $query->where('user_id', auth()->id());
                }),
            ],
            'email_secondary' => 'nullable|email|max:255',
            'phone_primary' => 'nullable|string|max:50',
            'phone_secondary' => 'nullable|string|max:50',
            'company' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'Please enter a first name.',
            'last_name.required' => 'Please enter a last name.',
            'email_primary.email' => 'Please enter a valid email address.',
            'email_primary.unique' => 'A person with this email already exists.',
            'email_secondary.email' => 'Please enter a valid secondary email address.',
        ];
    }
}
