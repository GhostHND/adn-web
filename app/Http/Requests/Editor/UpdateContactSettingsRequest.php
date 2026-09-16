<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = [
            'phone',
            'whatsapp',
            'email',
            'address',
            'business_hours',
            'map_url',
            'facebook',
            'instagram',
            'tiktok',
        ];

        $normalized = [];

        foreach (
            $fields
            as
            $field
        ) {
            $value =
                trim(
                    (string)
                    $this->input(
                        $field,
                        ''
                    )
                );

            if (
                $field ===
                'email'
            ) {
                $value =
                    mb_strtolower(
                        $value
                    );
            }

            $normalized[
                $field
            ] =
                $value !==
                ''
                    ? $value
                    : null;
        }

        $this->merge(
            $normalized
        );
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'nullable',
                'string',
                'max:40',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:100',
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:190',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'business_hours' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'map_url' => [
                'nullable',
                'url',
                'max:2000',
            ],

            'facebook' => [
                'nullable',
                'url',
                'max:2000',
            ],

            'instagram' => [
                'nullable',
                'url',
                'max:2000',
            ],

            'tiktok' => [
                'nullable',
                'url',
                'max:2000',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'phone' =>
                'teléfono',

            'whatsapp' =>
                'WhatsApp',

            'email' =>
                'correo electrónico',

            'address' =>
                'dirección',

            'business_hours' =>
                'horarios',

            'map_url' =>
                'ubicación',

            'facebook' =>
                'Facebook',

            'instagram' =>
                'Instagram',

            'tiktok' =>
                'TikTok',
        ];
    }
}