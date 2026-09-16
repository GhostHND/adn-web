<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'eyebrow' => [
                'nullable',
                'string',
                'max:120',
            ],

            'summary' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                    'hidden',
                ]),
            ],

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'canonical_url' => [
                'nullable',
                'url',
                'max:1000',
            ],

            'sections' => [
                'required',
                'array',
            ],

            'sections.*.id' => [
                'required',
                'integer',
                Rule::exists(
                    'page_sections',
                    'id'
                )->whereNull(
                    'deleted_at'
                ),
            ],

            'sections.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sections.*.subtitle' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sections.*.body' => [
                'nullable',
                'string',
                'max:10000',
            ],

            /*
            |--------------------------------------------------------------------------
            | IMAGEN PRINCIPAL
            |--------------------------------------------------------------------------
            */

            'sections.*.media_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'media',
                    'id'
                )
                    ->where(
                        'is_public',
                        true
                    )
                    ->whereNull(
                        'deleted_at'
                    ),
            ],

            /*
            |--------------------------------------------------------------------------
            | IMÁGENES DE APOYO
            |--------------------------------------------------------------------------
            */

            'sections.*.support_media_ids' => [
                'present',
                'array',
                'max:6',
            ],

            'sections.*.support_media_ids.*' => [
                'required',
                'integer',
                'distinct',
                Rule::exists(
                    'media',
                    'id'
                )
                    ->where(
                        'is_public',
                        true
                    )
                    ->whereNull(
                        'deleted_at'
                    ),
            ],

            /*
            |--------------------------------------------------------------------------
            | ESTADO / ORDEN
            |--------------------------------------------------------------------------
            */

            'sections.*.sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
            ],

            'sections.*.active' => [
                'required',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | CONTENIDO ESTRUCTURADO
            |--------------------------------------------------------------------------
            */

            'sections.*.content' => [
                'nullable',
                'array',
            ],

            'sections.*.content.items' => [
                'nullable',
                'array',
                'max:20',
            ],

            'sections.*.content.items.*.title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'sections.*.content.items.*.label' => [
                'nullable',
                'string',
                'max:120',
            ],

            'sections.*.content.items.*.text' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'sections.*.content.items.*.icon' => [
                'nullable',
                'string',
                'max:60',
            ],

            /*
            |--------------------------------------------------------------------------
            | CONFIGURACIÓN
            |--------------------------------------------------------------------------
            */

            'sections.*.settings' => [
                'nullable',
                'array',
            ],

            'sections.*.settings.cta_label' => [
                'nullable',
                'string',
                'max:120',
            ],

            'sections.*.settings.cta_url' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'title' =>
                'título',

            'eyebrow' =>
                'encabezado',

            'summary' =>
                'resumen',

            'status' =>
                'estado',

            'meta_title' =>
                'título SEO',

            'meta_description' =>
                'descripción SEO',

            'canonical_url' =>
                'URL canónica',

            'sections.*.title' =>
                'título de sección',

            'sections.*.subtitle' =>
                'subtítulo de sección',

            'sections.*.body' =>
                'contenido de sección',

            'sections.*.media_id' =>
                'imagen principal',

            'sections.*.support_media_ids' =>
                'imágenes de apoyo',

            'sections.*.support_media_ids.*' =>
                'imagen de apoyo',

            'sections.*.sort_order' =>
                'orden de sección',
        ];
    }
}