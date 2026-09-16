<?php

namespace App\Http\Requests\Editor;

use App\Models\PortfolioProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePortfolioProjectRequest extends FormRequest
{
    /**
     * Autoriza la creación desde el Editor.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Reglas de validación para crear un proyecto.
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique(
                    'portfolio_projects',
                    'slug'
                )->whereNull(
                    'deleted_at'
                ),
            ],

            'client_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'excerpt' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'description' => [
                'nullable',
                'string',
                'max:20000',
            ],

            'category_id' => [
                'required',
                'integer',
                Rule::exists(
                    'portfolio_categories',
                    'id'
                )->whereNull(
                    'deleted_at'
                ),
            ],

            'main_media_id' => [
                'required',
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

            'gallery_media_ids' => [
                'present',
                'array',
                'max:20',
            ],

            'gallery_media_ids.*' => [
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

            'project_date' => [
                'nullable',
                'date',
            ],

            'status' => [
                'required',
                Rule::in([
                    PortfolioProject::STATUS_DRAFT,
                    PortfolioProject::STATUS_PUBLISHED,
                ]),
            ],

            'featured' => [
                'required',
                'boolean',
            ],

            'sort_order' => [
                'required',
                'integer',
                'min:0',
                'max:65535',
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
        ];
    }

    /**
     * Nombres amigables de los campos.
     */
    public function attributes(): array
    {
        return [
            'title' =>
                'título',

            'slug' =>
                'slug',

            'client_name' =>
                'cliente',

            'excerpt' =>
                'resumen',

            'description' =>
                'descripción',

            'category_id' =>
                'categoría',

            'main_media_id' =>
                'imagen principal',

            'gallery_media_ids' =>
                'galería',

            'gallery_media_ids.*' =>
                'imagen de galería',

            'project_date' =>
                'fecha del proyecto',

            'status' =>
                'estado',

            'featured' =>
                'proyecto destacado',

            'sort_order' =>
                'orden',

            'meta_title' =>
                'título SEO',

            'meta_description' =>
                'descripción SEO',
        ];
    }
}