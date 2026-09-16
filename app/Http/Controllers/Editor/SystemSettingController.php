<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\UpdateSystemSettingsRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SystemSettingController extends Controller
{
    public function index(): Response
    {
        $settings =
            Setting::query()
                ->whereIn(
                    'group',
                    [
                        'business',
                        'contact',
                        'social',
                        'seo',
                    ]
                )
                ->get()
                ->keyBy(
                    fn (
                        Setting $setting
                    ): string =>
                        $setting->group
                        .
                        '.'
                        .
                        $setting->key
                );

        return Inertia::render(
            'Editor/System/Settings/Index',
            [
                'settings' => [
                    'business' => [
                        'business_name' =>
                            $settings
                                ->get(
                                    'business.business_name'
                                )
                                ?->value,

                        'business_tagline' =>
                            $settings
                                ->get(
                                    'business.business_tagline'
                                )
                                ?->value,
                    ],

                    'contact' => [
                        'phone' =>
                            $settings
                                ->get(
                                    'contact.phone'
                                )
                                ?->value,

                        'whatsapp' =>
                            $settings
                                ->get(
                                    'contact.whatsapp'
                                )
                                ?->value,

                        'email' =>
                            $settings
                                ->get(
                                    'contact.email'
                                )
                                ?->value,

                        'address' =>
                            $settings
                                ->get(
                                    'contact.address'
                                )
                                ?->value,

                        'business_hours' =>
                            $settings
                                ->get(
                                    'contact.business_hours'
                                )
                                ?->value,

                        'map_url' =>
                            $settings
                                ->get(
                                    'contact.map_url'
                                )
                                ?->value,
                    ],

                    'social' => [
                        'facebook' =>
                            $settings
                                ->get(
                                    'social.facebook'
                                )
                                ?->value,

                        'instagram' =>
                            $settings
                                ->get(
                                    'social.instagram'
                                )
                                ?->value,

                        'tiktok' =>
                            $settings
                                ->get(
                                    'social.tiktok'
                                )
                                ?->value,
                    ],

                    'seo' => [
                        'default_meta_title' =>
                            $settings
                                ->get(
                                    'seo.default_meta_title'
                                )
                                ?->value,

                        'default_meta_description' =>
                            $settings
                                ->get(
                                    'seo.default_meta_description'
                                )
                                ?->value,
                    ],
                ],
            ]
        );
    }

    public function update(
        UpdateSystemSettingsRequest $request
    ): RedirectResponse {
        $validated =
            $request->validated();

        $definitions = [
            /*
            |--------------------------------------------------------------------------
            | EMPRESA
            |--------------------------------------------------------------------------
            */

            [
                'group' =>
                    'business',

                'key' =>
                    'business_name',

                'value' =>
                    $validated[
                        'business_name'
                    ],

                'type' =>
                    'text',

                'is_public' =>
                    true,

                'sort_order' =>
                    10,
            ],

            [
                'group' =>
                    'business',

                'key' =>
                    'business_tagline',

                'value' =>
                    $validated[
                        'business_tagline'
                    ]
                    ?? null,

                'type' =>
                    'text',

                'is_public' =>
                    true,

                'sort_order' =>
                    20,
            ],

            /*
            |--------------------------------------------------------------------------
            | CONTACTO
            |--------------------------------------------------------------------------
            */

            [
                'group' =>
                    'contact',

                'key' =>
                    'phone',

                'value' =>
                    $validated[
                        'phone'
                    ]
                    ?? null,

                'type' =>
                    'text',

                'is_public' =>
                    true,

                'sort_order' =>
                    30,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'whatsapp',

                'value' =>
                    $validated[
                        'whatsapp'
                    ]
                    ?? null,

                'type' =>
                    'text',

                'is_public' =>
                    true,

                'sort_order' =>
                    40,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'email',

                'value' =>
                    $validated[
                        'email'
                    ]
                    ?? null,

                'type' =>
                    'email',

                'is_public' =>
                    true,

                'sort_order' =>
                    50,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'business_hours',

                'value' =>
                    $validated[
                        'business_hours'
                    ]
                    ?? null,

                'type' =>
                    'textarea',

                'is_public' =>
                    true,

                'sort_order' =>
                    50,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'address',

                'value' =>
                    $validated[
                        'address'
                    ]
                    ?? null,

                'type' =>
                    'textarea',

                'is_public' =>
                    true,

                'sort_order' =>
                    60,
            ],

            [
                'group' =>
                    'contact',

                'key' =>
                    'map_url',

                'value' =>
                    $validated[
                        'map_url'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    60,
            ],

            /*
            |--------------------------------------------------------------------------
            | REDES SOCIALES
            |--------------------------------------------------------------------------
            */

            [
                'group' =>
                    'social',

                'key' =>
                    'tiktok',

                'value' =>
                    $validated[
                        'tiktok'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    30,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'facebook',

                'value' =>
                    $validated[
                        'facebook'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    70,
            ],

            [
                'group' =>
                    'social',

                'key' =>
                    'instagram',

                'value' =>
                    $validated[
                        'instagram'
                    ]
                    ?? null,

                'type' =>
                    'url',

                'is_public' =>
                    true,

                'sort_order' =>
                    80,
            ],

            /*
            |--------------------------------------------------------------------------
            | SEO
            |--------------------------------------------------------------------------
            */

            [
                'group' =>
                    'seo',

                'key' =>
                    'default_meta_title',

                'value' =>
                    $validated[
                        'default_meta_title'
                    ],

                'type' =>
                    'text',

                'is_public' =>
                    false,

                'sort_order' =>
                    90,
            ],

            [
                'group' =>
                    'seo',

                'key' =>
                    'default_meta_description',

                'value' =>
                    $validated[
                        'default_meta_description'
                    ]
                    ?? null,

                'type' =>
                    'textarea',

                'is_public' =>
                    false,

                'sort_order' =>
                    100,
            ],
        ];

        DB::transaction(
            function () use (
                $definitions
            ): void {
                foreach (
                    $definitions
                    as
                    $definition
                ) {
                    Setting::query()
                        ->updateOrCreate(
                            [
                                'group' =>
                                    $definition[
                                        'group'
                                    ],

                                'key' =>
                                    $definition[
                                        'key'
                                    ],
                            ],
                            [
                                'value' =>
                                    $definition[
                                        'value'
                                    ],

                                'type' =>
                                    $definition[
                                        'type'
                                    ],

                                'is_public' =>
                                    $definition[
                                        'is_public'
                                    ],

                                'sort_order' =>
                                    $definition[
                                        'sort_order'
                                    ],
                            ]
                        );
                }
            }
        );

        return back()
            ->with(
                'success',
                'La configuración general se actualizó correctamente.'
            );
    }
}