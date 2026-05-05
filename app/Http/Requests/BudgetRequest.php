<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where(fn ($q) => $q->where('user_id', $this->user()->id)->where('type', 'expense')),
            ],
            'limit_amount' => 'required|numeric|min:0.01|max:99999999.99',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2020|max:2100',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategorija je obavezna.',
            'category_id.exists' => 'Kategorija mora biti vasa rashodna kategorija.',
            'limit_amount.required' => 'Iznos je obavezan.',
            'limit_amount.min' => 'Iznos mora biti veci od 0.',
            'month.required' => 'Mesec je obavezan.',
            'year.required' => 'Godina je obavezna.',
        ];
    }
}
