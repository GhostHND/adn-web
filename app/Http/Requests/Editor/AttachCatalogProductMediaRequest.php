<?php

namespace App\Http\Requests\Editor;

use Illuminate\Foundation\Http\FormRequest;

class AttachCatalogProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'media_id' => [
                'required',
                'integer',
                'exists:media,id',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'media_id' => 'imagen',
        ];
    }
}