<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:income,expense',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->where('user_id', $this->user()->id),
            ],
            'amount' => 'required|numeric|min:0.01|max:99999999.99',
            'transaction_date' => 'required|date|before_or_equal:today',
            'note' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Tip transakcije je obavezan.',
            'category_id.required' => 'Kategorija je obavezna.',
            'category_id.exists' => 'Izabrana kategorija ne postoji.',
            'amount.required' => 'Iznos je obavezan.',
            'amount.min' => 'Iznos mora biti veci od 0.',
            'transaction_date.required' => 'Datum je obavezan.',
            'transaction_date.before_or_equal' => 'Datum ne sme biti u buducnosti.',
        ];
    }
}
