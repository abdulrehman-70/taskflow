<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],

            'status' => [
                'nullable',
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
                'nullable',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                    'urgent',
                ]),
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

            'sort_by' => [
                'nullable',
                Rule::in([
                    'created_at',
                    'due_date',
                    'priority',
                    'status',
                    'title',
                ]),
            ],

            'sort_direction' => [
                'nullable',
                Rule::in(['asc', 'desc']),
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
            ],
        ];
    }
}
