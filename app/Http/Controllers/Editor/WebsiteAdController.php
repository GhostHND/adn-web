<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\SaveWebsiteAdRequest;
use App\Models\WebsiteAd;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class WebsiteAdController extends Controller
{
    public function index(): Response
    {
        $ads =
            WebsiteAd::query()
                ->orderBy(
                    'placement'
                )
                ->orderBy(
                    'sort_order'
                )
                ->orderByDesc(
                    'id'
                )
                ->get()
                ->map(
                    fn (
                        WebsiteAd $ad
                    ): array =>
                        $this->serialize(
                            $ad
                        )
                )
                ->values()
                ->all();

        $summary = [
            'total' =>
                WebsiteAd::query()
                    ->count(),

            'active' =>
                WebsiteAd::query()
                    ->visible()
                    ->count(),

            'scheduled' =>
                WebsiteAd::query()
                    ->where(
                        'active',
                        true
                    )
                    ->whereNotNull(
                        'starts_at'
                    )
                    ->where(
                        'starts_at',
                        '>',
                        now()
                    )
                    ->count(),

            'inactive' =>
                WebsiteAd::query()
                    ->where(
                        'active',
                        false
                    )
                    ->count(),
        ];

        return Inertia::render(
            'Editor/Promotions/Index',
            [
                'ads' =>
                    $ads,

                'summary' =>
                    $summary,

                'placements' =>
                    WebsiteAd::placementOptions(),

                'platforms' =>
                    WebsiteAd::platformOptions(),
            ]
        );
    }

    public function store(
        SaveWebsiteAdRequest $request
    ): RedirectResponse {
        $validated =
            $request->validated();

        $image =
            $request->file(
                'image'
            );

        abort_unless(
            $image,
            422,
            'Debes seleccionar un arte para la publicidad.'
        );

        $validated[
            'image_path'
        ] =
            $this->storeImage(
                $image
            );

        unset(
            $validated[
                'image'
            ]
        );

        WebsiteAd::create(
            $validated
        );

        return back()
            ->with(
                'success',
                'Publicidad creada correctamente.'
            );
    }

    public function update(
        SaveWebsiteAdRequest $request,
        WebsiteAd $ad
    ): RedirectResponse {
        $validated =
            $request->validated();

        $oldImagePath =
            $ad->image_path;

        $newImage =
            $request->file(
                'image'
            );

        unset(
            $validated[
                'image'
            ]
        );

        if (
            $newImage
        ) {
            $validated[
                'image_path'
            ] =
                $this->storeImage(
                    $newImage
                );
        }

        $ad->update(
            $validated
        );

        if (
            $newImage
            &&
            $oldImagePath !==
                $ad->image_path
        ) {
            Storage::disk(
                'public'
            )->delete(
                $oldImagePath
            );
        }

        return back()
            ->with(
                'success',
                'Publicidad actualizada correctamente.'
            );
    }

    public function destroy(
        WebsiteAd $ad
    ): RedirectResponse {
        /*
        |--------------------------------------------------------------------------
        | ARCHIVADO
        |--------------------------------------------------------------------------
        |
        | Conservamos físicamente el arte para poder recuperarlo si
        | posteriormente agregamos restauración de publicidad.
        |
        */

        $ad->delete();

        return back()
            ->with(
                'success',
                'Publicidad archivada correctamente.'
            );
    }

    private function storeImage(
        UploadedFile $image
    ): string {
        return $image->store(
            'website-promotions/'
            .
            now()->format(
                'Y/m'
            ),
            'public'
        );
    }

    private function serialize(
        WebsiteAd $ad
    ): array {
        return [
            'id' =>
                $ad->id,

            'name' =>
                $ad->name,

            'placement' =>
                $ad->placement,

            'platform' =>
                $ad->platform,

            'image_url' =>
                Storage::disk(
                    'public'
                )->url(
                    $ad->image_path
                ),

            'alt_text' =>
                $ad->alt_text,

            'link_url' =>
                $ad->link_url,

            'cta_label' =>
                $ad->cta_label,

            'open_in_new_tab' =>
                (bool)
                $ad->open_in_new_tab,

            'active' =>
                (bool)
                $ad->active,

            'sort_order' =>
                $ad->sort_order,

            'starts_at' =>
                $ad->starts_at
                    ?->toIso8601String(),

            'ends_at' =>
                $ad->ends_at
                    ?->toIso8601String(),

            'visibility' =>
                $this->visibility(
                    $ad
                ),
        ];
    }

    private function visibility(
        WebsiteAd $ad
    ): string {
        if (
            !$ad->active
        ) {
            return 'inactive';
        }

        if (
            $ad->starts_at
            &&
            $ad->starts_at
                ->isFuture()
        ) {
            return 'scheduled';
        }

        if (
            $ad->ends_at
            &&
            $ad->ends_at
                ->isPast()
        ) {
            return 'expired';
        }

        return 'active';
    }
}