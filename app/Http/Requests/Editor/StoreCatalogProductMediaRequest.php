<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class StoreCatalogProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'image',
                'max:20480',
                'mimes:jpg,jpeg,png,webp',
            ],

            'title' => [
                'nullable',
                'string',
                'max:255',
            ],

            'alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'file' => 'imagen',
            'title' => 'título',
            'alt_text' => 'texto alternativo',
        ];
    }
}