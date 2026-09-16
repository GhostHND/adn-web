<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'page_section_media',
            function (
                Blueprint $table
            ): void {
                $table->id();

                $table
                    ->foreignId(
                        'page_section_id'
                    )
                    ->constrained(
                        'page_sections'
                    )
                    ->cascadeOnDelete();

                $table
                    ->foreignId(
                        'media_id'
                    )
                    ->constrained(
                        'media'
                    )
                    ->cascadeOnDelete();

                $table
                    ->unsignedSmallInteger(
                        'sort_order'
                    )
                    ->default(
                        0
                    );

                $table->timestamps();

                $table->unique([
                    'page_section_id',
                    'media_id',
                ]);

                $table->index([
                    'page_section_id',
                    'sort_order',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'page_section_media'
        );
    }
};