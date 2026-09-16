<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePortfolioCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $category =
            $this->route(
                'category'
            );

        return [
            'name' => [
                'required',
                'string',
                'max:150',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:180',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(
                    'portfolio_categories',
                    'slug'
                )
                    ->ignore(
                        $category
                    )
                    ->whereNull(
                        'deleted_at'
                    ),
            ],

            'description' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
            ],

            'active' => [
                'required',
                'boolean',
            ],
        ];
    }
}