<?php

namespace App\Http\Requests\Api\V1;

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
            'name' => ['required', 'string', 'max:255'],
            'logic' => ['nullable', 'string'],
            'next_step' => ['nullable', 'string'],
            'start_date' => ['required', 'date'],
            'due_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'lead_id' => [
                'nullable',
                Rule::exists('people', 'id')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                }),
            ],
            'status' => ['required', Rule::in(Activity::STATUS_IN_PROGRESS, Activity::STATUS_COMPLETED, Activity::STATUS_CANCELLED)],
            'parties' => ['nullable', 'array', 'max:5'],
            'parties.*' => [
                Rule::exists('people', 'id')->where(function ($query) {
                    return $query->where('user_id', $this->user()->id);
                }),
            ],
        ];
    }
}
