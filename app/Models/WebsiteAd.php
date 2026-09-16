<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteAd extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const PLACEMENT_CATALOG_SIDEBAR =
        'catalog_sidebar';

    public const PLACEMENT_CATALOG_MOBILE =
        'catalog_mobile';

    public const PLACEMENT_CATALOG_HORIZONTAL =
        'catalog_horizontal';

    public const PLACEMENT_PRODUCT_RECTANGLE =
        'product_rectangle';

    public const PLACEMENT_SITE_WIDE =
        'site_wide';

    public const PLACEMENT_ABOUT_INLINE =
        'about_inline';

    public const PLACEMENT_SERVICES_INLINE =
        'services_inline';

    public const PLATFORM_FACEBOOK =
        'facebook';

    public const PLATFORM_INSTAGRAM =
        'instagram';

    public const PLATFORM_TIKTOK =
        'tiktok';

    public const PLATFORM_WHATSAPP =
        'whatsapp';

    public const PLATFORM_GENERAL =
        'general';

    protected $fillable = [
        'name',
        'placement',
        'platform',
        'image_path',
        'alt_text',
        'link_url',
        'cta_label',
        'open_in_new_tab',
        'active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'open_in_new_tab' =>
                'boolean',

            'active' =>
                'boolean',

            'sort_order' =>
                'integer',

            'starts_at' =>
                'datetime',

            'ends_at' =>
                'datetime',
        ];
    }

    public function scopeVisible(
        Builder $query
    ): Builder {
        return $query
            ->where(
                'active',
                true
            )
            ->where(
                function (
                    Builder $query
                ): void {
                    $query
                        ->whereNull(
                            'starts_at'
                        )
                        ->orWhere(
                            'starts_at',
                            '<=',
                            now()
                        );
                }
            )
            ->where(
                function (
                    Builder $query
                ): void {
                    $query
                        ->whereNull(
                            'ends_at'
                        )
                        ->orWhere(
                            'ends_at',
                            '>=',
                            now()
                        );
                }
            );
    }

    public function scopePlacement(
        Builder $query,
        string $placement
    ): Builder {
        return $query->where(
            'placement',
            $placement
        );
    }

    public static function placementOptions(): array
    {
        return [
            [
                'value' =>
                    self::PLACEMENT_CATALOG_SIDEBAR,

                'label' =>
                    'Catálogo · lateral',

                'size' =>
                    '300 × 600',

                'description' =>
                    'Banner vertical discreto al costado del catálogo en pantallas grandes.',
            ],

            [
                'value' =>
                    self::PLACEMENT_CATALOG_MOBILE,

                'label' =>
                    'Catálogo · móvil',

                'size' =>
                    '300 × 250',

                'description' =>
                    'Pieza rectangular pensada para teléfonos.',
            ],

            [
                'value' =>
                    self::PLACEMENT_CATALOG_HORIZONTAL,

                'label' =>
                    'Catálogo · horizontal',

                'size' =>
                    '728 × 90',

                'description' =>
                    'Banner horizontal para espacios internos del catálogo.',
            ],

            [
                'value' =>
                    self::PLACEMENT_PRODUCT_RECTANGLE,

                'label' =>
                    'Producto · rectangular',

                'size' =>
                    '336 × 280',

                'description' =>
                    'Publicidad complementaria dentro del detalle de un producto.',
            ],

            [
                'value' =>
                    self::PLACEMENT_SITE_WIDE,

                'label' =>
                    'Inicio · panorámico',

                'size' =>
                    '970 × 250',

                'description' =>
                    'Pieza editorial panorámica para la página principal.',
            ],

            [
                'value' =>
                    self::PLACEMENT_ABOUT_INLINE,

                'label' =>
                    'Sobre nosotros · integrado',

                'size' =>
                    '970 × 250',

                'description' =>
                    'Promoción sutil integrada entre el contenido de Sobre nosotros.',
            ],

            [
                'value' =>
                    self::PLACEMENT_SERVICES_INLINE,

                'label' =>
                    'Servicios · integrado',

                'size' =>
                    '970 × 250',

                'description' =>
                    'Promoción sutil integrada entre el contenido de Servicios.',
            ],
        ];
    }

    public static function platformOptions(): array
    {
        return [
            [
                'value' =>
                    self::PLATFORM_FACEBOOK,

                'label' =>
                    'Facebook',
            ],

            [
                'value' =>
                    self::PLATFORM_INSTAGRAM,

                'label' =>
                    'Instagram',
            ],

            [
                'value' =>
                    self::PLATFORM_TIKTOK,

                'label' =>
                    'TikTok',
            ],

            [
                'value' =>
                    self::PLATFORM_WHATSAPP,

                'label' =>
                    'WhatsApp',
            ],

            [
                'value' =>
                    self::PLATFORM_GENERAL,

                'label' =>
                    'General',
            ],
        ];
    }
}