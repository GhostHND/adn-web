<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatalogProductFieldRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'label' => [
                'required',
                'string',
                'max:180',
            ],

            'field_type' => [
                'required',

                Rule::in([
                    'text',
                    'textarea',
                    'number',
                    'select',
                    'radio',
                    'checkbox',
                    'file',
                ]),
            ],

            'placeholder' => [
                'nullable',
                'string',
                'max:255',
            ],

            'help_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'required' => [
                'required',
                'boolean',
            ],

            'min_value' => [
                'nullable',
                'numeric',
            ],

            'max_value' => [
                'nullable',
                'numeric',
            ],

            'step' => [
                'nullable',
                'numeric',
                'gt:0',
            ],

            'default_value' => [
                'nullable',
                'string',
                'max:500',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:999999',
            ],

            'active' => [
                'required',
                'boolean',
            ],

            'options' => [
                'nullable',
                'array',
                'max:50',
            ],

            'options.*.label' => [
                'nullable',
                'string',
                'max:180',
            ],

            'options.*.value' => [
                'nullable',
                'string',
                'max:180',
            ],

            'options.*.extra_price' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'options.*.active' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'label' => 'etiqueta',
            'field_type' => 'tipo de campo',
            'placeholder' => 'texto de ejemplo',
            'help_text' => 'texto de ayuda',
            'unit' => 'unidad',
            'required' => 'campo obligatorio',
            'min_value' => 'valor mínimo',
            'max_value' => 'valor máximo',
            'step' => 'incremento',
            'default_value' => 'valor predeterminado',
            'sort_order' => 'orden',
            'active' => 'estado',
            'options' => 'opciones',
        ];
    }
}