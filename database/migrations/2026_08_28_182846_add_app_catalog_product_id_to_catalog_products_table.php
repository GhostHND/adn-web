<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'catalog_products',
            function (
                Blueprint $table
            ): void {
                $table
                    ->foreignId(
                        'app_catalog_product_id'
                    )
                    ->nullable()
                    ->after(
                        'id'
                    )
                    ->unique()
                    ->constrained(
                        'app_catalog_products'
                    )
                    ->nullOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'catalog_products',
            function (
                Blueprint $table
            ): void {
                $table->dropConstrainedForeignId(
                    'app_catalog_product_id'
                );
            }
        );
    }
};