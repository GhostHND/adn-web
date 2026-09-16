<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Editor\StoreCatalogProductFieldRequest;
use App\Http\Requests\Editor\UpdateCatalogProductFieldRequest;
use App\Models\CatalogProduct;
use App\Models\CatalogProductField;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CatalogProductFieldController extends Controller
{
    public function index(
        CatalogProduct $product
    ): Response {
        $product->load([
            'category:id,name',
            'fields' => fn ($query) =>
                $query->orderBy('sort_order')
                    ->orderBy('id'),

            'fields.options' => fn ($query) =>
                $query->orderBy('sort_order')
                    ->orderBy('id'),
        ]);

        return Inertia::render(
            'Editor/Catalog/Products/Fields',
            [
                'product' => $product,
            ]
        );
    }

    public function store(
        StoreCatalogProductFieldRequest $request,
        CatalogProduct $product
    ): RedirectResponse {
        $validated =
            $request->validated();

        $options =
            $validated['options']
            ?? [];

        unset(
            $validated['options']
        );

        DB::transaction(
            function () use (
                $product,
                $validated,
                $options
            ): void {
                $field =
                    $product
                        ->fields()
                        ->create([
                            ...$validated,

                            'field_key' =>
                                $this->generateUniqueFieldKey(
                                    $product,
                                    $validated['label']
                                ),
                        ]);

                $this->syncOptions(
                    $field,
                    $options
                );
            }
        );

        return back()
            ->with(
                'success',
                'Campo de cotización creado correctamente.'
            );
    }

    public function update(
        UpdateCatalogProductFieldRequest $request,
        CatalogProduct $product,
        CatalogProductField $field
    ): RedirectResponse {
        $this->ensureBelongsToProduct(
            $product,
            $field
        );

        $validated =
            $request->validated();

        $options =
            $validated['options']
            ?? [];

        unset(
            $validated['options']
        );

        DB::transaction(
            function () use (
                $field,
                $validated,
                $options
            ): void {
                /*
                |--------------------------------------------------------------------------
                | field_key NO cambia.
                |--------------------------------------------------------------------------
                |
                | De esta manera una modificación del nombre visible no rompe
                | futuras referencias ni los snapshots de las solicitudes.
                |
                */

                $field->update(
                    $validated
                );

                $this->syncOptions(
                    $field,
                    $options
                );
            }
        );

        return back()
            ->with(
                'success',
                'Campo actualizado correctamente.'
            );
    }

    public function destroy(
        CatalogProduct $product,
        CatalogProductField $field
    ): RedirectResponse {
        $this->ensureBelongsToProduct(
            $product,
            $field
        );

        $field->delete();

        return back()
            ->with(
                'success',
                'Campo eliminado correctamente.'
            );
    }

    private function generateUniqueFieldKey(
        CatalogProduct $product,
        string $label
    ): string {
        $base =
            Str::of(
                $label
            )
                ->ascii()
                ->lower()
                ->replaceMatches(
                    '/[^a-z0-9]+/',
                    '_'
                )
                ->trim('_')
                ->toString();

        if ($base === '') {
            $base =
                'campo';
        }

        $fieldKey =
            $base;

        $counter =
            2;

        while (
            $product
                ->fields()
                ->where(
                    'field_key',
                    $fieldKey
                )
                ->exists()
        ) {
            $fieldKey =
                $base .
                '_' .
                $counter;

            $counter++;
        }

        return $fieldKey;
    }

    private function syncOptions(
        CatalogProductField $field,
        array $options
    ): void {
        /*
        |--------------------------------------------------------------------------
        | Solo SELECT y RADIO requieren un catálogo de opciones.
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $field->field_type,
                [
                    'select',
                    'radio',
                ],
                true
            )
        ) {
            $field
                ->options()
                ->delete();

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Las opciones no son referenciadas por solicitudes históricas.
        |
        | Las solicitudes almacenarán label/value como snapshot, por lo que
        | podemos reconstruir las opciones del campo con seguridad.
        |--------------------------------------------------------------------------
        */

        $field
            ->options()
            ->delete();

        $sortOrder =
            10;

        foreach ($options as $option) {
            $label =
                trim(
                    (string) (
                        $option['label']
                        ?? ''
                    )
                );

            if ($label === '') {
                continue;
            }

            $value =
                trim(
                    (string) (
                        $option['value']
                        ?? ''
                    )
                );

            if ($value === '') {
                $value =
                    Str::slug(
                        $label,
                        '_'
                    );
            }

            if ($value === '') {
                $value =
                    'opcion_' .
                    $sortOrder;
            }

            $field
                ->options()
                ->create([
                    'label' =>
                        $label,

                    'value' =>
                        $value,

                    'extra_price' =>
                        (float) (
                            $option['extra_price']
                            ?? 0
                        ),

                    'sort_order' =>
                        $sortOrder,

                    'active' =>
                        (bool) (
                            $option['active']
                            ?? true
                        ),
                ]);

            $sortOrder +=
                10;
        }
    }

    private function ensureBelongsToProduct(
        CatalogProduct $product,
        CatalogProductField $field
    ): void {
        abort_unless(
            (int) $field->catalog_product_id ===
                (int) $product->id,
            404
        );
    }
}