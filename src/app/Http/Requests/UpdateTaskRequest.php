<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => [
                'required',
                Rule::in([
                    'todo',
                    'in_progress',
                    'stuck',
                    'review',
                    'completed',
                    'cancelled',
                ]),
            ],
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high', 'urgent']),
            ],
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],
            'due_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
        ];
    }
}
