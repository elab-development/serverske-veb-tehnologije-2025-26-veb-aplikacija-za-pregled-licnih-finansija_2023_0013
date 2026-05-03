<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'type' => 'required|in:income,expense',
            'color' => 'required|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Naziv kategorije je obavezan.',
            'name.max' => 'Naziv ne sme biti duzi od 100 karaktera.',
            'type.required' => 'Tip kategorije je obavezan.',
            'type.in' => 'Tip mora biti prihod ili rashod.',
            'color.required' => 'Boja je obavezna.',
            'color.regex' => 'Boja mora biti u HEX formatu (npr. #16A34A).',
        ];
    }
}
