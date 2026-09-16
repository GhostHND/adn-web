<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatalogProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:200',
            ],

            'category_id' => [
                'required',
                'integer',

                Rule::exists(
                    'catalog_categories',
                    'id'
                )->whereNull('deleted_at'),
            ],

            'app_reference_code' => [
                'nullable',
                'string',
                'max:80',
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

            'quote_mode' => [
                'required',

                Rule::in([
                    'UNIT',
                    'AREA',
                    'LINEAR',
                    'CUSTOM',
                ]),
            ],

            'measurement_unit' => [
                'nullable',
                'string',
                'max:50',
            ],

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
            'name' => 'nombre',
            'category_id' => 'categoría',
            'app_reference_code' => 'referencia de la app',
            'short_description' => 'descripción corta',
            'description' => 'descripción',
            'features' => 'características',
            'price_visible' => 'visibilidad del precio',
            'price_from' => 'precio desde',
            'reference_price' => 'precio de referencia',
            'price_note' => 'nota del precio',
            'quote_mode' => 'modo de cotización',
            'measurement_unit' => 'unidad de medida',
            'status' => 'estado',
            'featured' => 'producto destacado',
            'show_on_home' => 'mostrar en inicio',
            'sort_order' => 'orden',
            'meta_title' => 'título SEO',
            'meta_description' => 'descripción SEO',
        ];
    }
}