<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_project_id',
        'media_id',
        'alt_text',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' =>
                'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(
            PortfolioProject::class,
            'portfolio_project_id'
        );
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(
            Media::class
        );
    }
}