<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuoteRequestStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    'new',
                    'reviewing',
                    'contacted',
                    'quoted',
                    'closed',
                    'cancelled',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' =>
                'Selecciona un estado.',

            'status.in' =>
                'El estado seleccionado no es válido.',
        ];
    }
}