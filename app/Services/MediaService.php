<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function store(
        UploadedFile $file,
        array $metadata = [],
        string $disk = 'public'
    ): Media {
        $extension =
            strtolower(
                $file->guessExtension()
                ?: $file->getClientOriginalExtension()
            );

        $filename =
            Str::uuid()->toString()
            .
            (
                $extension !== ''
                    ? '.' . $extension
                    : ''
            );

        $directory =
            'media/'
            . now()->format('Y')
            . '/'
            . now()->format('m');

        $path =
            Storage::disk($disk)
                ->putFileAs(
                    $directory,
                    $file,
                    $filename
                );

        $width = null;
        $height = null;

        if (
            str_starts_with(
                (string) $file->getMimeType(),
                'image/'
            )
        ) {
            $imageSize =
                @getimagesize(
                    $file->getRealPath()
                );

            if (
                is_array($imageSize)
                &&
                isset(
                    $imageSize[0],
                    $imageSize[1]
                )
            ) {
                $width =
                    (int) $imageSize[0];

                $height =
                    (int) $imageSize[1];
            }
        }

        $originalName =
            $file->getClientOriginalName();

        $defaultTitle =
            pathinfo(
                $originalName,
                PATHINFO_FILENAME
            );

        return Media::create([
            'uuid' =>
                Str::uuid()->toString(),

            'disk' =>
                $disk,

            'path' =>
                $path,

            'filename' =>
                $filename,

            'original_name' =>
                $originalName,

            'mime_type' =>
                $file->getMimeType(),

            'extension' =>
                $extension !== ''
                    ? $extension
                    : null,

            'title' =>
                $this->nullableString(
                    $metadata['title']
                    ?? $defaultTitle
                ),

            'alt_text' =>
                $this->nullableString(
                    $metadata['alt_text']
                    ?? null
                ),

            'caption' =>
                $this->nullableString(
                    $metadata['caption']
                    ?? null
                ),

            'width' =>
                $width,

            'height' =>
                $height,

            'size' =>
                $file->getSize(),

            'uploaded_by' =>
                auth()->id(),

            'is_public' =>
                (bool) (
                    $metadata['is_public']
                    ?? true
                ),
        ]);
    }

    public function url(
        Media $media
    ): string {
        return Storage::disk(
            $media->disk
        )->url(
            $media->path
        );
    }

    public function delete(
        Media $media
    ): void {
        Storage::disk(
            $media->disk
        )->delete(
            $media->path
        );

        $media->forceDelete();
    }

    private function nullableString(
        mixed $value
    ): ?string {
        if (
            $value === null
        ) {
            return null;
        }

        $value =
            trim(
                (string) $value
            );

        return $value !== ''
            ? $value
            : null;
    }
}