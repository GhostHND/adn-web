<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreMediaRequest;
use App\Models\CatalogCategory;
use App\Models\CatalogProduct;
use App\Models\CatalogProductImage;
use App\Models\Media;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PortfolioImage;
use App\Models\PortfolioProject;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function index(
        Request $request
    ): Response {
        $search =
            trim(
                (string) $request->input(
                    'search',
                    ''
                )
            );

        $type =
            trim(
                (string) $request->input(
                    'type',
                    ''
                )
            );

        $media =
            Media::query()
                ->when(
                    $search !== '',
                    function ($query) use ($search): void {
                        $query->where(
                            function ($inner) use ($search): void {
                                $inner
                                    ->where(
                                        'title',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'original_name',
                                        'like',
                                        "%{$search}%"
                                    )
                                    ->orWhere(
                                        'alt_text',
                                        'like',
                                        "%{$search}%"
                                    );
                            }
                        );
                    }
                )
                ->when(
                    $type === 'image',
                    fn ($query) =>
                        $query->where(
                            'mime_type',
                            'like',
                            'image/%'
                        )
                )
                ->when(
                    $type === 'document',
                    fn ($query) =>
                        $query->where(
                            'mime_type',
                            'application/pdf'
                        )
                )
                ->latest()
                ->paginate(24)
                ->withQueryString();

        $media->through(
            fn (Media $item): array => [
                'id' =>
                    $item->id,

                'uuid' =>
                    $item->uuid,

                'title' =>
                    $item->title,

                'original_name' =>
                    $item->original_name,

                'mime_type' =>
                    $item->mime_type,

                'extension' =>
                    $item->extension,

                'alt_text' =>
                    $item->alt_text,

                'caption' =>
                    $item->caption,

                'width' =>
                    $item->width,

                'height' =>
                    $item->height,

                'size' =>
                    $item->size,

                'url' =>
                    $this->mediaService
                        ->url(
                            $item
                        ),

                'created_at' =>
                    $item->created_at,
            ]
        );

        $summary = [
            'total' =>
                Media::count(),

            'images' =>
                Media::query()
                    ->where(
                        'mime_type',
                        'like',
                        'image/%'
                    )
                    ->count(),

            'documents' =>
                Media::query()
                    ->where(
                        'mime_type',
                        'application/pdf'
                    )
                    ->count(),
        ];

        return Inertia::render(
            'Editor/Media/Index',
            [
                'media' =>
                    $media,

                'summary' =>
                    $summary,

                'filters' => [
                    'search' =>
                        $search,

                    'type' =>
                        $type,
                ],
            ]
        );
    }

    public function store(
        StoreMediaRequest $request
    ): RedirectResponse {
        $this->mediaService->store(
            $request->file('file'),
            [
                'title' =>
                    $request->input(
                        'title'
                    ),

                'alt_text' =>
                    $request->input(
                        'alt_text'
                    ),

                'caption' =>
                    $request->input(
                        'caption'
                    ),

                'is_public' =>
                    true,
            ]
        );

        return back()
            ->with(
                'success',
                'Archivo agregado a Multimedia correctamente.'
            );
    }

    public function destroy(
        Media $media
    ): RedirectResponse {
        if (
            $this->usageCount(
                $media
            ) > 0
        ) {
            return back()
                ->withErrors([
                    'delete' =>
                        'Este archivo está siendo utilizado en el sitio y no puede eliminarse todavía.',
                ]);
        }

        $this->mediaService->delete(
            $media
        );

        return back()
            ->with(
                'success',
                'Archivo eliminado correctamente.'
            );
    }

    private function usageCount(
        Media $media
    ): int {
        return
            CatalogProduct::query()
                ->where(
                    'main_media_id',
                    $media->id
                )
                ->count()
            +
            CatalogProductImage::query()
                ->where(
                    'media_id',
                    $media->id
                )
                ->count()
            +
            CatalogCategory::query()
                ->where(
                    'media_id',
                    $media->id
                )
                ->count()
            +
            Page::query()
                ->where(
                    'og_media_id',
                    $media->id
                )
                ->count()
            +
            PageSection::query()
                ->where(
                    'media_id',
                    $media->id
                )
                ->count()
            +
            Service::query()
                ->where(
                    'main_media_id',
                    $media->id
                )
                ->count()
            +
            ServiceImage::query()
                ->where(
                    'media_id',
                    $media->id
                )
                ->count()
            +
            PortfolioProject::query()
                ->where(
                    'main_media_id',
                    $media->id
                )
                ->count()
            +
            PortfolioImage::query()
                ->where(
                    'media_id',
                    $media->id
                )
                ->count();
    }
}