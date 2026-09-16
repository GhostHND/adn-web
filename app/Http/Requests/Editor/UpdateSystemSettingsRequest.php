<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSystemSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $fields = [
            'business_name',
            'business_tagline',

            'phone',
            'whatsapp',
            'email',
            'address',
            'business_hours',
            'map_url',

            'facebook',
            'instagram',
            'tiktok',

            'default_meta_title',
            'default_meta_description',
        ];

        $normalized = [];

        foreach (
            $fields
            as
            $field
        ) {
            $value =
                $this->input(
                    $field
                );

            if (
                !is_string(
                    $value
                )
            ) {
                continue;
            }

            $value =
                trim(
                    $value
                );

            $normalized[
                $field
            ] =
                $value === ''
                    ? null
                    : $value;
        }

        $this->merge(
            $normalized
        );
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | EMPRESA
            |--------------------------------------------------------------------------
            */

            'business_name' => [
                'required',
                'string',
                'max:150',
            ],

            'business_tagline' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */

            'phone' => [
                'nullable',
                'string',
                'max:60',
            ],

            'whatsapp' => [
                'nullable',
                'string',
                'max:60',
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'business_hours' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'map_url' => [
                'nullable',
                'url',
                'max:2048',
            ],

            /*
            |--------------------------------------------------------------------------
            | REDES SOCIALES
            |--------------------------------------------------------------------------
            */

            'facebook' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'instagram' => [
                'nullable',
                'url',
                'max:2048',
            ],

            'tiktok' => [
                'nullable',
                'url',
                'max:2048',
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'default_meta_title' => [
                'required',
                'string',
                'max:255',
            ],

            'default_meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' =>
                'El nombre comercial es obligatorio.',

            'business_name.max' =>
                'El nombre comercial no puede superar los 150 caracteres.',

            'email.email' =>
                'Ingresa un correo electrónico válido.',

            'map_url.url' =>
                'Ingresa una URL válida para la ubicación.',

            'facebook.url' =>
                'Ingresa una URL válida de Facebook.',

            'instagram.url' =>
                'Ingresa una URL válida de Instagram.',

            'tiktok.url' =>
                'Ingresa una URL válida de TikTok.',

            'default_meta_title.required' =>
                'El título SEO predeterminado es obligatorio.',

            'default_meta_description.max' =>
                'La descripción SEO no puede superar los 500 caracteres.',
        ];
    }
}