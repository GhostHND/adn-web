<?php

namespace App\Services;

use App\Models\CatalogProduct;
use App\Models\CatalogProductField;
use App\Models\QuoteRequest;
use App\Models\QuoteRequestFile;
use App\Models\QuoteRequestItem;
use App\Models\QuoteRequestValue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class QuoteRequestService
{
    public function create(
        CatalogProduct $product,
        array $data,
        array $values = [],
        array $files = [],
        ?string $ipAddress = null,
        ?string $userAgent = null
    ): QuoteRequest {
        $product->load([
            'fields' => fn ($query) =>
                $query
                    ->where(
                        'active',
                        true
                    )
                    ->orderBy(
                        'sort_order'
                    )
                    ->orderBy(
                        'id'
                    ),

            'fields.options' => fn ($query) =>
                $query
                    ->where(
                        'active',
                        true
                    )
                    ->orderBy(
                        'sort_order'
                    )
                    ->orderBy(
                        'id'
                    ),
        ]);

        $quantity =
            max(
                1,
                (int) $data['quantity']
            );

        $this->validateDynamicFields(
            $product,
            $values,
            $files,
            $quantity
        );

        $storedPaths =
            [];

        try {
            return DB::transaction(
                function () use (
                    $product,
                    $data,
                    $values,
                    $files,
                    $ipAddress,
                    $userAgent,
                    $quantity,
                    &$storedPaths
                ): QuoteRequest {
                    $publicToken =
                        Str::uuid()
                            ->toString();

                    $quote =
                        QuoteRequest::create([
                            'request_number' =>
                                'TMP-'
                                . Str::uuid()
                                    ->toString(),

                            'public_token' =>
                                $publicToken,

                            'status' =>
                                'new',

                            'client_name' =>
                                $data['client_name'],

                            'phone' =>
                                $data['phone'],

                            'whatsapp' =>
                                $data['whatsapp']
                                ?: $data['phone'],

                            'email' =>
                                $data['email']
                                ?: null,

                            'company' =>
                                $data['company']
                                ?: null,

                            'notes' =>
                                $data['notes']
                                ?? null,

                            'source' =>
                                'website',

                            'privacy_consent' =>
                                true,

                            'ip_address' =>
                                $ipAddress,

                            'user_agent' =>
                                $userAgent,

                            'app_sync_status' =>
                                'pending',

                            'sync_attempts' =>
                                0,
                        ]);

                    $quote->update([
                        'request_number' =>
                            sprintf(
                                'SOL-WEB-%06d',
                                $quote->id
                            ),
                    ]);

                    $item =
                        QuoteRequestItem::create([
                            'quote_request_id' =>
                                $quote->id,

                            'catalog_product_id' =>
                                $product->id,

                            'product_code_snapshot' =>
                                $product->code,

                            'product_name_snapshot' =>
                                $product->name,

                            'quantity' =>
                                $quantity,

                            'unit_snapshot' =>
                                $product->measurement_unit,

                            'quote_mode_snapshot' =>
                                $product->quote_mode,

                            'reference_price_snapshot' =>
                                $product->reference_price,

                            'notes' =>
                                null,

                            'sort_order' =>
                                10,
                        ]);

                    foreach (
                        $product->fields
                        as $field
                    ) {
                        if (
                            $field->field_type ===
                            'file'
                        ) {
                            $this->storeFileFields(
                                $quote,
                                $item,
                                $field,
                                $files,
                                $storedPaths
                            );

                            continue;
                        }

                        $this->storeValueField(
                            $item,
                            $field,
                            $values
                        );
                    }

                    return $quote->fresh();
                }
            );
        } catch (Throwable $exception) {
            if (
                count(
                    $storedPaths
                ) > 0
            ) {
                Storage::disk(
                    'local'
                )->delete(
                    $storedPaths
                );
            }

            throw $exception;
        }
    }

    private function validateDynamicFields(
        CatalogProduct $product,
        array $values,
        array $files,
        int $quantity
    ): void {
        $rules =
            [];

        $attributes =
            [];

        foreach (
            $product->fields
            as $field
        ) {
            $key =
                $field->field_key;

            if (
                $field->field_type ===
                'file'
            ) {
                $rules[
                    "files.{$key}"
                ] = [
                    $field->required
                        ? 'required'
                        : 'nullable',

                    'array',

                    $field->required
                        ? 'min:1'
                        : 'min:0',

                    'max:'
                    . $quantity,
                ];

                $rules[
                    "files.{$key}.*"
                ] = [
                    'file',
                    'max:51200',
                ];

                $attributes[
                    "files.{$key}"
                ] =
                    $field->label;

                $attributes[
                    "files.{$key}.*"
                ] =
                    $field->label;

                continue;
            }

            $fieldRules =
                [];

            if (
                $field->field_type ===
                'checkbox'
            ) {
                $fieldRules[] =
                    $field->required
                        ? 'present'
                        : 'nullable';

                $fieldRules[] =
                    'boolean';
            } else {
                $fieldRules[] =
                    $field->required
                        ? 'required'
                        : 'nullable';
            }

            switch (
                $field->field_type
            ) {
                case 'number':
                    $fieldRules[] =
                        'numeric';

                    if (
                        $field->min_value !==
                        null
                    ) {
                        $fieldRules[] =
                            'min:'
                            . $field->min_value;
                    }

                    if (
                        $field->max_value !==
                        null
                    ) {
                        $fieldRules[] =
                            'max:'
                            . $field->max_value;
                    }

                    break;

                case 'select':
                case 'radio':
                    $allowedValues =
                        $field
                            ->options
                            ->pluck(
                                'value'
                            )
                            ->map(
                                fn ($value): string =>
                                    (string) $value
                            )
                            ->all();

                    $fieldRules[] =
                        Rule::in(
                            $allowedValues
                        );

                    break;

                case 'textarea':
                    $fieldRules[] =
                        'string';

                    $fieldRules[] =
                        'max:5000';

                    break;

                case 'text':
                    $fieldRules[] =
                        'string';

                    $fieldRules[] =
                        'max:1000';

                    break;

                case 'checkbox':
                    break;

                default:
                    $fieldRules[] =
                        'string';

                    $fieldRules[] =
                        'max:1000';

                    break;
            }

            $rules[
                "values.{$key}"
            ] =
                $fieldRules;

            $attributes[
                "values.{$key}"
            ] =
                $field->label;
        }

        Validator::make(
            [
                'values' =>
                    $values,

                'files' =>
                    $files,
            ],
            $rules,
            [
                'files.*.required' =>
                    'Adjunta al menos un archivo.',

                'files.*.array' =>
                    'Los archivos seleccionados no son válidos.',

                'files.*.min' =>
                    'Adjunta al menos un archivo.',

                'files.*.max' =>
                    'Puedes adjuntar como máximo :max archivos para esta cantidad.',

                'files.*.*.file' =>
                    'Uno de los archivos seleccionados no es válido.',

                'files.*.*.max' =>
                    'Cada archivo puede pesar hasta 50 MB.',
            ],
            $attributes
        )->validate();
    }

    private function storeValueField(
        QuoteRequestItem $item,
        CatalogProductField $field,
        array $values
    ): void {
        $key =
            $field->field_key;

        $rawValue =
            $values[
                $key
            ]
            ?? null;

        if (
            $rawValue ===
                null
            ||
            (
                is_string(
                    $rawValue
                )
                &&
                trim(
                    $rawValue
                ) ===
                    ''
            )
        ) {
            return;
        }

        $valueText =
            null;

        $valueJson = [
            'field_type' =>
                $field->field_type,

            'field_id_snapshot' =>
                $field->id,

            'raw_value' =>
                $rawValue,
        ];

        if (
            in_array(
                $field->field_type,
                [
                    'select',
                    'radio',
                ],
                true
            )
        ) {
            $option =
                $field
                    ->options
                    ->firstWhere(
                        'value',
                        (string) $rawValue
                    );

            if (
                $option
            ) {
                $valueText =
                    $option->label;

                $valueJson[
                    'selected_option'
                ] = [
                    'value' =>
                        $option->value,

                    'label' =>
                        $option->label,

                    'extra_price' =>
                        $option->extra_price,
                ];
            } else {
                $valueText =
                    (string) $rawValue;
            }
        } elseif (
            $field->field_type ===
            'checkbox'
        ) {
            $checked =
                filter_var(
                    $rawValue,
                    FILTER_VALIDATE_BOOLEAN
                );

            $valueText =
                $checked
                    ? 'Sí'
                    : 'No';

            $valueJson[
                'raw_value'
            ] =
                $checked;
        } else {
            $valueText =
                (string) $rawValue;
        }

        QuoteRequestValue::create([
            'quote_request_item_id' =>
                $item->id,

            'field_key' =>
                $field->field_key,

            'label_snapshot' =>
                $field->label,

            'value_text' =>
                $valueText,

            'value_json' =>
                $valueJson,

            'unit_snapshot' =>
                $field->unit,

            'sort_order' =>
                $field->sort_order,
        ]);
    }

    private function storeFileFields(
        QuoteRequest $quote,
        QuoteRequestItem $item,
        CatalogProductField $field,
        array $files,
        array &$storedPaths
    ): void {
        $fieldFiles =
            $files[
                $field->field_key
            ]
            ?? [];

        if (
            $fieldFiles
            instanceof
            UploadedFile
        ) {
            $fieldFiles = [
                $fieldFiles,
            ];
        }

        if (
            !is_array(
                $fieldFiles
            )
        ) {
            return;
        }

        foreach (
            $fieldFiles
            as $index => $file
        ) {
            if (
                !(
                    $file
                    instanceof
                    UploadedFile
                )
            ) {
                continue;
            }

            $extension =
                strtolower(
                    (string) $file->extension()
                );

            $filename =
                Str::uuid()
                    ->toString()
                .
                (
                    $extension !==
                    ''
                        ? '.'
                            . $extension
                        : ''
                );

            $directory =
                'quote-requests/'
                . now()->format(
                    'Y'
                )
                . '/'
                . now()->format(
                    'm'
                )
                . '/'
                . $quote->public_token
                . '/'
                . $field->field_key;

            $path =
                $file->storeAs(
                    $directory,
                    $filename,
                    'local'
                );

            $storedPaths[] =
                $path;

            QuoteRequestFile::create([
                'quote_request_id' =>
                    $quote->id,

                'quote_request_item_id' =>
                    $item->id,

                'path' =>
                    $path,

                'original_name' =>
                    $file->getClientOriginalName(),

                'mime_type' =>
                    $file->getMimeType(),

                'size' =>
                    $file->getSize(),
            ]);

            QuoteRequestValue::create([
                'quote_request_item_id' =>
                    $item->id,

                'field_key' =>
                    $field->field_key,

                'label_snapshot' =>
                    $field->label,

                'value_text' =>
                    $file->getClientOriginalName(),

                'value_json' => [
                    'field_type' =>
                        'file',

                    'field_id_snapshot' =>
                        $field->id,

                    'file_index' =>
                        $index + 1,

                    'original_name' =>
                        $file->getClientOriginalName(),

                    'mime_type' =>
                        $file->getMimeType(),

                    'size' =>
                        $file->getSize(),

                    'path' =>
                        $path,
                ],

                'unit_snapshot' =>
                    null,

                'sort_order' =>
                    $field->sort_order
                    +
                    $index,
            ]);
        }
    }
}