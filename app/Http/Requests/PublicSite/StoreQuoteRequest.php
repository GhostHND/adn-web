<?php

namespace App\Http\Requests\PublicSite;

use Illuminate\Foundation\Http\FormRequest;

class StoreQuoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'client_name' =>
                trim(
                    (string) $this->input(
                        'client_name',
                        ''
                    )
                ),

            'phone' =>
                trim(
                    (string) $this->input(
                        'phone',
                        ''
                    )
                ),

            'whatsapp' =>
                trim(
                    (string) $this->input(
                        'whatsapp',
                        ''
                    )
                ),

            'email' =>
                trim(
                    (string) $this->input(
                        'email',
                        ''
                    )
                ),

            'company' =>
                trim(
                    (string) $this->input(
                        'company',
                        ''
                    )
                ),
        ]);
    }

    public function rules(): array
    {
        return [
            'client_name' => [
                'required',
                'string',
                'max:160',
            ],

            'phone' => [
                'required',
                'string',
                'min:8',
                'max:40',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:40',
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:190',
            ],

            'company' => [
                'nullable',
                'string',
                'max:190',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'privacy_consent' => [
                'accepted',
            ],

            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:100000',
            ],

            'values' => [
                'nullable',
                'array',
            ],

            'files' => [
                'nullable',
                'array',
            ],

            'files.*' => [
                'nullable',
                'array',
            ],

            'files.*.*' => [
                'file',
                'max:51200',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'client_name.required' =>
                'Escribe tu nombre para continuar.',

            'phone.required' =>
                'Escribe un número de teléfono.',

            'phone.min' =>
                'El número de teléfono parece incompleto.',

            'email.email' =>
                'Escribe un correo electrónico válido.',

            'privacy_consent.accepted' =>
                'Debes aceptar el uso de tus datos para enviar la solicitud.',

            'quantity.required' =>
                'Indica la cantidad que necesitas.',

            'quantity.min' =>
                'La cantidad debe ser al menos 1.',

            'files.*.array' =>
                'Los archivos seleccionados no tienen un formato válido.',

            'files.*.*.file' =>
                'Uno de los archivos seleccionados no es válido.',

            'files.*.*.max' =>
                'Cada archivo puede pesar hasta 50 MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'client_name' =>
                'nombre',

            'phone' =>
                'teléfono',

            'whatsapp' =>
                'WhatsApp',

            'email' =>
                'correo electrónico',

            'company' =>
                'empresa',

            'privacy_consent' =>
                'consentimiento',

            'quantity' =>
                'cantidad',
        ];
    }
}