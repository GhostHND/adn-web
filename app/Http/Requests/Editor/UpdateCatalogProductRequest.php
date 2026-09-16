<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatalogProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | INFORMACIÓN WEB
            |--------------------------------------------------------------------------
            |
            | Nombre, código APP y unidad NO se reciben.
            | Esos datos son administrados exclusivamente desde ADN APP.
            |
            */

            'category_id' => [
                'required',
                'integer',

                Rule::exists(
                    'catalog_categories',
                    'id'
                )->whereNull(
                    'deleted_at'
                ),
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'features' => [
                'nullable',
                'array',
                'max:30',
            ],

            'features.*' => [
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | PRECIO PÚBLICO
            |--------------------------------------------------------------------------
            |
            | Este es únicamente el precio que opcionalmente se muestra en
            | la web. Los costos y precios internos de ADN APP nunca llegan
            | a este proyecto.
            |
            */

            'price_visible' => [
                'required',
                'boolean',
            ],

            'price_from' => [
                'required',
                'boolean',
            ],

            'reference_price' => [
                'nullable',
                'numeric',
                'min:0',
                'max:999999999999.99',
            ],

            'price_note' => [
                'nullable',
                'string',
                'max:255',
            ],

            /*
            |--------------------------------------------------------------------------
            | COTIZACIÓN WEB
            |--------------------------------------------------------------------------
            */

            'quote_mode' => [
                'required',

                Rule::in([
                    'UNIT',
                    'AREA',
                    'LINEAR',
                    'CUSTOM',
                ]),
            ],

            /*
            |--------------------------------------------------------------------------
            | PUBLICACIÓN
            |--------------------------------------------------------------------------
            */

            'status' => [
                'required',

                Rule::in([
                    'draft',
                    'published',
                    'hidden',
                ]),
            ],

            'featured' => [
                'required',
                'boolean',
            ],

            'show_on_home' => [
                'required',
                'boolean',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            'meta_title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'meta_description' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'category_id' =>
                'categoría web',

            'short_description' =>
                'descripción corta',

            'description' =>
                'descripción',

            'features' =>
                'características',

            'price_visible' =>
                'visibilidad del precio',

            'price_from' =>
                'precio desde',

            'reference_price' =>
                'precio de referencia',

            'price_note' =>
                'nota del precio',

            'quote_mode' =>
                'modo de cotización',

            'status' =>
                'estado',

            'featured' =>
                'producto destacado',

            'show_on_home' =>
                'mostrar en inicio',

            'sort_order' =>
                'orden',

            'meta_title' =>
                'título SEO',

            'meta_description' =>
                'descripción SEO',
        ];
    }
}