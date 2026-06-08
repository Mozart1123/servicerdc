<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class SendServiceRequestForm extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:1000'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'email'       => ['nullable', 'email', 'max:255'],
            'budget_min'  => ['nullable', 'numeric', 'min:0'],
            'budget_max'  => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'urgency'     => ['nullable', 'in:low,medium,high,urgent'],
        ];
    }

    public function messages(): array
    {
        return [
            'budget_max.gte' => 'Le budget maximum doit être supérieur ou égal au budget minimum.',
        ];
    }
}
