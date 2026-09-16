<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'slug',
        'name',
        'title',
        'eyebrow',
        'summary',
        'status',
        'meta_title',
        'meta_description',
        'canonical_url',
        'og_media_id',
        'sort_order',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function sections(): HasMany
    {
        return $this
            ->hasMany(
                PageSection::class
            )
            ->orderBy('sort_order');
    }

    public function ogMedia(): BelongsTo
    {
        return $this->belongsTo(
            Media::class,
            'og_media_id'
        );
    }
}