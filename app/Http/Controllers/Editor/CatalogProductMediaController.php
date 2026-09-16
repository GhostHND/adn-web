<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\AttachCatalogProductMediaRequest;
use App\Http\Requests\Editor\StoreCatalogProductMediaRequest;
use App\Models\CatalogProduct;
use App\Models\CatalogProductImage;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CatalogProductMediaController extends Controller
{
    public function __construct(
        private readonly MediaService $mediaService
    ) {
    }

    public function index(
        CatalogProduct $product
    ): Response {
        $product->load([
            'category:id,name',
            'mainMedia',

            'images' => fn ($query) =>
                $query
                    ->orderBy('sort_order')
                    ->orderBy('id'),

            'images.media',
        ]);

        $attachedMediaIds =
            $product
                ->images
                ->pluck('media_id')
                ->map(
                    fn ($id): int =>
                        (int) $id
                )
                ->all();

        $availableMedia =
            Media::query()
                ->where(
                    'mime_type',
                    'like',
                    'image/%'
                )
                ->when(
                    count(
                        $attachedMediaIds
                    ) > 0,
                    fn ($query) =>
                        $query->whereNotIn(
                            'id',
                            $attachedMediaIds
                        )
                )
                ->latest()
                ->limit(80)
                ->get()
                ->map(
                    fn (Media $media): array =>
                        $this->serializeMedia(
                            $media
                        )
                )
                ->values();

        return Inertia::render(
            'Editor/Catalog/Products/Media',
            [
                'product' => [
                    'id' =>
                        $product->id,

                    'code' =>
                        $product->code,

                    'name' =>
                        $product->name,

                    'slug' =>
                        $product->slug,

                    'category' =>
                        $product->category,

                    'main_media_id' =>
                        $product->main_media_id,

                    'main_media' =>
                        $product->mainMedia
                            ? $this->serializeMedia(
                                $product->mainMedia
                            )
                            : null,

                    'images' =>
                        $product
                            ->images
                            ->map(
                                fn (
                                    CatalogProductImage $image
                                ): array => [
                                    'id' =>
                                        $image->id,

                                    'media_id' =>
                                        $image->media_id,

                                    'sort_order' =>
                                        $image->sort_order,

                                    'alt_text' =>
                                        $image->alt_text,

                                    'media' =>
                                        $this->serializeMedia(
                                            $image->media
                                        ),
                                ]
                            )
                            ->values(),
                ],

                'availableMedia' =>
                    $availableMedia,
            ]
        );
    }

    public function store(
        StoreCatalogProductMediaRequest $request,
        CatalogProduct $product
    ): RedirectResponse {
        DB::transaction(
            function () use (
                $request,
                $product
            ): void {
                $media =
                    $this->mediaService
                        ->store(
                            $request->file(
                                'file'
                            ),
                            [
                                'title' =>
                                    $request->input(
                                        'title'
                                    )
                                    ?: $product->name,

                                'alt_text' =>
                                    $request->input(
                                        'alt_text'
                                    )
                                    ?: $product->name,

                                'is_public' =>
                                    true,
                            ]
                        );

                $this->attachMedia(
                    $product,
                    $media,
                    $request->input(
                        'alt_text'
                    )
                    ?: $product->name
                );
            }
        );

        return back()
            ->with(
                'success',
                'Imagen agregada al producto correctamente.'
            );
    }

    public function attachExisting(
        AttachCatalogProductMediaRequest $request,
        CatalogProduct $product
    ): RedirectResponse {
        $media =
            Media::query()
                ->findOrFail(
                    $request->integer(
                        'media_id'
                    )
                );

        abort_unless(
            str_starts_with(
                (string) $media->mime_type,
                'image/'
            ),
            422,
            'El archivo seleccionado no es una imagen.'
        );

        $alreadyAttached =
            $product
                ->images()
                ->where(
                    'media_id',
                    $media->id
                )
                ->exists();

        if (
            $alreadyAttached
        ) {
            return back()
                ->with(
                    'success',
                    'La imagen ya estaba asociada a este producto.'
                );
        }

        DB::transaction(
            function () use (
                $product,
                $media
            ): void {
                $this->attachMedia(
                    $product,
                    $media,
                    $media->alt_text
                    ?: $media->title
                    ?: $product->name
                );
            }
        );

        return back()
            ->with(
                'success',
                'Imagen existente agregada al producto.'
            );
    }

    public function setMain(
        CatalogProduct $product,
        Media $media
    ): RedirectResponse {
        $belongsToProduct =
            $product
                ->images()
                ->where(
                    'media_id',
                    $media->id
                )
                ->exists();

        abort_unless(
            $belongsToProduct,
            404
        );

        $product->update([
            'main_media_id' =>
                $media->id,
        ]);

        return back()
            ->with(
                'success',
                'Imagen principal actualizada.'
            );
    }

    public function destroy(
        CatalogProduct $product,
        CatalogProductImage $image
    ): RedirectResponse {
        abort_unless(
            (int) $image->catalog_product_id ===
                (int) $product->id,
            404
        );

        DB::transaction(
            function () use (
                $product,
                $image
            ): void {
                $removedMediaId =
                    $image->media_id;

                $image->delete();

                if (
                    (int) $product->main_media_id ===
                    (int) $removedMediaId
                ) {
                    $nextMediaId =
                        $product
                            ->images()
                            ->orderBy(
                                'sort_order'
                            )
                            ->orderBy(
                                'id'
                            )
                            ->value(
                                'media_id'
                            );

                    $product->update([
                        'main_media_id' =>
                            $nextMediaId,
                    ]);
                }
            }
        );

        return back()
            ->with(
                'success',
                'Imagen retirada del producto. El archivo continúa disponible en Multimedia.'
            );
    }

    private function attachMedia(
        CatalogProduct $product,
        Media $media,
        ?string $altText = null
    ): CatalogProductImage {
        $nextOrder =
            (
                (int) $product
                    ->images()
                    ->max(
                        'sort_order'
                    )
            ) + 10;

        $image =
            $product
                ->images()
                ->create([
                    'media_id' =>
                        $media->id,

                    'alt_text' =>
                        $altText,

                    'sort_order' =>
                        $nextOrder,
                ]);

        if (
            $product->main_media_id ===
            null
        ) {
            $product->update([
                'main_media_id' =>
                    $media->id,
            ]);
        }

        return $image;
    }

    private function serializeMedia(
        Media $media
    ): array {
        return [
            'id' =>
                $media->id,

            'title' =>
                $media->title,

            'original_name' =>
                $media->original_name,

            'mime_type' =>
                $media->mime_type,

            'extension' =>
                $media->extension,

            'alt_text' =>
                $media->alt_text,

            'width' =>
                $media->width,

            'height' =>
                $media->height,

            'size' =>
                $media->size,

            'url' =>
                $this->mediaService
                    ->url(
                        $media
                    ),
        ];
    }
}