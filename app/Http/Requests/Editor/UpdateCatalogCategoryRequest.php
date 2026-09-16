<?php

namespace App\Http\Requests\Editor;

use App\Models\CatalogCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCatalogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var CatalogCategory|null $category */
        $category =
            $this->route('category');

        return [
            'name' => [
                'required',
                'string',
                'max:180',
            ],

            'description' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'parent_id' => [
                'nullable',
                'integer',

                Rule::exists(
                    'catalog_categories',
                    'id'
                )->whereNull('deleted_at'),

                Rule::notIn(
                    array_filter([
                        $category?->id,
                    ])
                ),
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'published',
                    'hidden',
                ]),
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
            'description' => 'descripción',
            'parent_id' => 'categoría superior',
            'status' => 'estado',
            'sort_order' => 'orden',
            'meta_title' => 'título SEO',
            'meta_description' => 'descripción SEO',
        ];
    }
}