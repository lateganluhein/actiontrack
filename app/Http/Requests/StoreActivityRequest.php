<?php

namespace App\Http\Requests;

use App\Models\Activity;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'logic' => 'nullable|string|max:10000',
            'next_step' => 'nullable|string|max:10000',
            'start_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'lead_id' => 'required|exists:people,id',
            'status' => ['required', Rule::in([
                Activity::STATUS_IN_PROGRESS,
                Activity::STATUS_COMPLETED,
                Activity::STATUS_CANCELLED,
            ])],
            'parties' => 'nullable|array|max:5',
            'parties.*' => 'exists:people,id',
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
            'name.required' => 'Please enter an activity name.',
            'start_date.required' => 'Please select a start date.',
            'due_date.after_or_equal' => 'Due date must be on or after the start date.',
            'lead_id.required' => 'Please select a lead person.',
            'lead_id.exists' => 'The selected lead person does not exist.',
            'parties.max' => 'You can select up to 5 participants.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Auto-calculate due date if not provided (30 days from start)
        if ($this->filled('start_date') && !$this->filled('due_date')) {
            $this->merge([
                'due_date' => now()->parse($this->start_date)->addDays(30)->format('Y-m-d'),
            ]);
        }
    }
}
