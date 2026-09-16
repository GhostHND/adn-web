<?php

namespace App\Http\Requests\Editor;

use App\Models\WebsiteAd;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveWebsiteAdRequest extends FormRequest
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
                'max:150',
            ],

            'placement' => [
                'required',

                Rule::in(
                    collect(
                        WebsiteAd::placementOptions()
                    )
                        ->pluck(
                            'value'
                        )
                        ->all()
                ),
            ],

            'platform' => [
                'nullable',

                Rule::in(
                    collect(
                        WebsiteAd::platformOptions()
                    )
                        ->pluck(
                            'value'
                        )
                        ->all()
                ),
            ],

            'image' => [
                'nullable',
                'file',
                'mimes:jpg,jpeg,png,webp,avif',
                'max:12288',
            ],

            'alt_text' => [
                'nullable',
                'string',
                'max:255',
            ],

            'link_url' => [
                'nullable',
                'url',
                'max:1000',
            ],

            'cta_label' => [
                'nullable',
                'string',
                'max:80',
            ],

            'open_in_new_tab' => [
                'required',
                'boolean',
            ],

            'active' => [
                'required',
                'boolean',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'name' =>
                'nombre interno',

            'placement' =>
                'ubicación',

            'platform' =>
                'red social',

            'image' =>
                'arte',

            'alt_text' =>
                'texto alternativo',

            'link_url' =>
                'enlace',

            'cta_label' =>
                'llamado a la acción',

            'open_in_new_tab' =>
                'abrir en nueva pestaña',

            'active' =>
                'estado',

            'sort_order' =>
                'orden',

            'starts_at' =>
                'fecha de inicio',

            'ends_at' =>
                'fecha de finalización',
        ];
    }
}