<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PageSection extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'page_id',
        'section_key',
        'section_type',
        'title',
        'subtitle',
        'body',
        'content',
        'media_id',
        'settings',
        'sort_order',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'content' =>
                'array',

            'settings' =>
                'array',

            'sort_order' =>
                'integer',

            'active' =>
                'boolean',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(
            Page::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | IMAGEN PRINCIPAL
    |--------------------------------------------------------------------------
    */

    public function media(): BelongsTo
    {
        return $this->belongsTo(
            Media::class
        );
    }

    /*
    |--------------------------------------------------------------------------
    | IMÁGENES DE APOYO
    |--------------------------------------------------------------------------
    |
    | Se utilizan para composiciones editoriales, ejemplos de trabajos,
    | mosaicos visuales y galerías pequeñas dentro de una sección.
    |
    | No representan un portafolio completo.
    |
    */

    public function supportMedia(): BelongsToMany
    {
        return $this
            ->belongsToMany(
                Media::class,
                'page_section_media',
                'page_section_id',
                'media_id'
            )
            ->withPivot(
                'sort_order'
            )
            ->withTimestamps()
            ->orderBy(
                'page_section_media.sort_order'
            )
            ->orderBy(
                'media.id'
            );
    }
}