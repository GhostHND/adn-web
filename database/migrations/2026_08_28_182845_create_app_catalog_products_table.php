<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'app_catalog_products',
            function (
                Blueprint $table
            ): void {
                $table->id();

                $table
                    ->unsignedBigInteger(
                        'app_catalog_item_id'
                    )
                    ->unique();

                $table
                    ->string(
                        'item_code',
                        50
                    )
                    ->unique();

                $table->string(
                    'name',
                    150
                );

                $table
                    ->text(
                        'description'
                    )
                    ->nullable();

                $table->string(
                    'item_type',
                    30
                );

                $table
                    ->string(
                        'category',
                        100
                    )
                    ->nullable();

                $table->string(
                    'pricing_method',
                    30
                );

                $table
                    ->string(
                        'measurement_unit',
                        50
                    )
                    ->nullable();

                $table
                    ->boolean(
                        'active'
                    )
                    ->default(
                        true
                    );

                $table
                    ->timestamp(
                        'source_created_at'
                    )
                    ->nullable();

                $table
                    ->timestamp(
                        'source_updated_at'
                    )
                    ->nullable();

                $table
                    ->timestamp(
                        'source_deleted_at'
                    )
                    ->nullable();

                $table
                    ->timestamp(
                        'synced_at'
                    )
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'active',
                    'item_type',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'app_catalog_products'
        );
    }
};