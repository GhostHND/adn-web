<?php

namespace App\Http\Requests\PublicSite;

use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' =>
                trim(
                    (string) $this->input(
                        'name',
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

            'phone' =>
                trim(
                    (string) $this->input(
                        'phone',
                        ''
                    )
                ),

            'email' =>
                mb_strtolower(
                    trim(
                        (string) $this->input(
                            'email',
                            ''
                        )
                    )
                ),

            'preferred_contact' =>
                trim(
                    (string) $this->input(
                        'preferred_contact',
                        ''
                    )
                ),

            'subject' =>
                trim(
                    (string) $this->input(
                        'subject',
                        ''
                    )
                ),

            'message' =>
                trim(
                    (string) $this->input(
                        'message',
                        ''
                    )
                ),

            'website' =>
                trim(
                    (string) $this->input(
                        'website',
                        ''
                    )
                ),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'min:2',
                'max:160',
            ],

            'company' => [
                'nullable',
                'string',
                'max:190',
            ],

            'phone' => [
                'nullable',
                'string',
                'min:8',
                'max:40',

                'required_without:email',
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:190',

                'required_without:phone',
            ],

            'preferred_contact' => [
                'nullable',

                Rule::in([
                    'whatsapp',
                    'phone',
                    'email',
                ]),
            ],

            'subject' => [
                'required',
                'string',
                'min:3',
                'max:190',
            ],

            'message' => [
                'required',
                'string',
                'min:10',
                'max:5000',
            ],

            'privacy_consent' => [
                'accepted',
            ],

            /*
            |--------------------------------------------------------------------------
            | HONEYPOT
            |--------------------------------------------------------------------------
            |
            | Los usuarios reales nunca ven ni completan este campo.
            |
            */

            'website' => [
                'nullable',
                'string',
                'max:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | PROTECCIÓN TEMPORAL
            |--------------------------------------------------------------------------
            */

            'form_started_at' => [
                'required',
                'integer',
                'min:1',
            ],

            'form_token' => [
                'required',
                'string',
                'size:64',
            ],
        ];
    }

    public function after(): array
    {
        return [
            function (
                Validator $validator
            ): void {
                $startedAt =
                    (int) $this->input(
                        'form_started_at',
                        0
                    );

                $token =
                    strtolower(
                        trim(
                            (string) $this->input(
                                'form_token',
                                ''
                            )
                        )
                    );

                if (
                    $startedAt <=
                    0
                    ||
                    !preg_match(
                        '/^[a-f0-9]{64}$/',
                        $token
                    )
                ) {
                    return;
                }

                $key =
                    (string) config(
                        'app.key',
                        ''
                    );

                if (
                    $key ===
                    ''
                ) {
                    $validator
                        ->errors()
                        ->add(
                            'form_token',
                            'No fue posible validar el formulario.'
                        );

                    return;
                }

                $expected =
                    hash_hmac(
                        'sha256',
                        (string) $startedAt,
                        $key
                    );

                if (
                    !hash_equals(
                        $expected,
                        $token
                    )
                ) {
                    $validator
                        ->errors()
                        ->add(
                            'form_token',
                            'El formulario ya no es válido. Recarga la página e inténtalo nuevamente.'
                        );

                    return;
                }

                $elapsed =
                    now()->timestamp
                    -
                    $startedAt;

                if (
                    $elapsed <
                    3
                ) {
                    $validator
                        ->errors()
                        ->add(
                            'form_token',
                            'El formulario fue enviado demasiado rápido. Inténtalo nuevamente.'
                        );

                    return;
                }

                if (
                    $elapsed >
                    7200
                ) {
                    $validator
                        ->errors()
                        ->add(
                            'form_token',
                            'El formulario expiró. Recarga la página antes de enviarlo.'
                        );
                }
            },
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>
                'Escribe tu nombre.',

            'name.min' =>
                'El nombre parece incompleto.',

            'phone.required_without' =>
                'Escribe un teléfono o un correo electrónico.',

            'phone.min' =>
                'El número de teléfono parece incompleto.',

            'email.required_without' =>
                'Escribe un correo electrónico o un teléfono.',

            'email.email' =>
                'Escribe un correo electrónico válido.',

            'preferred_contact.in' =>
                'Selecciona una forma de contacto válida.',

            'subject.required' =>
                'Indica brevemente en qué podemos ayudarte.',

            'subject.min' =>
                'El asunto parece demasiado corto.',

            'message.required' =>
                'Escribe tu mensaje.',

            'message.min' =>
                'Cuéntanos un poco más sobre lo que necesitas.',

            'message.max' =>
                'El mensaje no puede superar los 5,000 caracteres.',

            'privacy_consent.accepted' =>
                'Debes aceptar el uso de tus datos para enviar el mensaje.',

            'website.max' =>
                'No fue posible procesar el formulario.',

            'form_started_at.required' =>
                'Recarga la página antes de enviar el formulario.',

            'form_token.required' =>
                'Recarga la página antes de enviar el formulario.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' =>
                'nombre',

            'company' =>
                'empresa',

            'phone' =>
                'teléfono',

            'email' =>
                'correo electrónico',

            'preferred_contact' =>
                'medio de contacto',

            'subject' =>
                'asunto',

            'message' =>
                'mensaje',

            'privacy_consent' =>
                'consentimiento',
        ];
    }
}