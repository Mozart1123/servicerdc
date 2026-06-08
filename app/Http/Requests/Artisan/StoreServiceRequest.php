<?php

namespace App\Http\Requests\Artisan;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->user_type, ['artisan'], true);
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'category'    => ['nullable', 'string', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'description' => ['required', 'string', 'min:20'],
            'price'       => ['required', 'numeric', 'min:0'],
            'location'    => ['required', 'string', 'max:255'],
            'status'      => ['sometimes', 'string', 'in:active,inactive'],
            'images'      => ['nullable', 'array'],
            'images.*'    => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'Le titre du service est obligatoire.',
            'description.required' => 'La description est obligatoire.',
            'description.min'      => 'La description doit contenir au moins 20 caractères.',
            'price.required'       => 'Le prix (ou estimation) est obligatoire.',
            'price.numeric'        => 'Le prix doit être un nombre.',
            'location.required'    => 'La ville/localisation est obligatoire.',
        ];
    }
}
