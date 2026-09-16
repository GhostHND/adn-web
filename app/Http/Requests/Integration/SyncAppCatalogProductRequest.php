<?php

namespace App\Http\Requests\Integration;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SyncAppCatalogProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'app_catalog_item_id' => [
                'required',
                'integer',
                'min:1',
            ],

            'item_code' => [
                'required',
                'string',
                'max:50',
            ],

            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'item_type' => [
                'required',

                Rule::in([
                    'product',
                    'service',
                    'custom',
                ]),
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'pricing_method' => [
                'required',

                Rule::in([
                    'AREA',
                    'UNIT',
                    'FIXED',
                    'LINEAR',
                    'COST_MARGIN',
                    'RESALE',
                    'MANUAL',
                ]),
            ],

            'measurement_unit' => [
                'nullable',
                'string',
                'max:50',
            ],

            'active' => [
                'required',
                'boolean',
            ],

            'source_created_at' => [
                'nullable',
                'date',
            ],

            'source_updated_at' => [
                'nullable',
                'date',
            ],

            'source_deleted_at' => [
                'nullable',
                'date',
            ],
        ];
    }
}