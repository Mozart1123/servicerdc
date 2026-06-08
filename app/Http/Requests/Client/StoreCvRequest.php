<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class StoreCvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'full_name'      => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255'],
            'phone_number'   => ['required', 'string', 'max:30'],
            'address'        => ['required', 'string', 'max:500'],
            'education'      => ['required'],
            'skills'         => ['required'],
            'experience'     => ['required'],
            'languages'      => ['required'],
            'cv_file_path'   => ['nullable', 'string', 'max:2000'],
            'portfolio_link' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required'    => 'Le nom complet est obligatoire.',
            'email.required'        => 'L\'adresse email est obligatoire.',
            'phone_number.required' => 'Le numéro de téléphone est obligatoire.',
            'address.required'      => 'L\'adresse est obligatoire.',
            'education.required'    => 'Veuillez renseigner vos formations.',
            'skills.required'       => 'Veuillez renseigner vos compétences.',
            'experience.required'   => 'Veuillez renseigner votre expérience.',
            'languages.required'    => 'Veuillez renseigner vos langues.',
            'portfolio_link.url'    => 'Le lien portfolio doit être une URL valide.',
        ];
    }
}
