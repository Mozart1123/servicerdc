<?php

namespace App\Http\Requests\Recruiter;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        return $user && in_array($user->user_type, ['recruiter', 'job_seeker'], true)
            || ($user && in_array($user->role, ['admin', 'super_admin'], true));
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['required', 'string', 'min:20'],
            'requirements'  => ['nullable', 'string'],
            'salary_range'  => ['nullable', 'string', 'max:100'],
            'contract_type' => ['required', 'in:full-time,part-time,internship,freelance,contract,CDI,CDD'],
            'location'      => ['required', 'string', 'max:255'],
            'category'      => ['required', 'string', 'max:100'],
            'deadline'      => ['required', 'date', 'after:today'],
            'status'        => ['sometimes', 'in:active,closed'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required'         => 'Le titre du poste est obligatoire.',
            'description.required'   => 'La description du poste est obligatoire.',
            'description.min'        => 'La description doit contenir au moins 20 caractères.',
            'contract_type.required' => 'Le type de contrat est obligatoire.',
            'contract_type.in'       => 'Type de contrat invalide.',
            'location.required'      => 'La localisation est obligatoire.',
            'category.required'      => 'La catégorie est obligatoire.',
            'deadline.required'      => 'La date limite de candidature est obligatoire.',
            'deadline.after'         => 'La date limite doit être dans le futur.',
        ];
    }
}
