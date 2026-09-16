<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'website_ads',
            function (
                Blueprint $table
            ): void {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | IDENTIFICACIÓN
                |--------------------------------------------------------------------------
                */

                $table
                    ->string(
                        'name',
                        150
                    );

                $table
                    ->string(
                        'placement',
                        60
                    )
                    ->index();

                $table
                    ->string(
                        'platform',
                        30
                    )
                    ->nullable()
                    ->index();

                /*
                |--------------------------------------------------------------------------
                | ARTE
                |--------------------------------------------------------------------------
                */

                $table
                    ->string(
                        'image_path',
                        500
                    );

                $table
                    ->string(
                        'alt_text',
                        255
                    )
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | DESTINO
                |--------------------------------------------------------------------------
                */

                $table
                    ->string(
                        'link_url',
                        1000
                    )
                    ->nullable();

                $table
                    ->string(
                        'cta_label',
                        80
                    )
                    ->nullable();

                $table
                    ->boolean(
                        'open_in_new_tab'
                    )
                    ->default(
                        true
                    );

                /*
                |--------------------------------------------------------------------------
                | PUBLICACIÓN
                |--------------------------------------------------------------------------
                */

                $table
                    ->boolean(
                        'active'
                    )
                    ->default(
                        true
                    )
                    ->index();

                $table
                    ->unsignedSmallInteger(
                        'sort_order'
                    )
                    ->default(
                        0
                    );

                $table
                    ->timestamp(
                        'starts_at'
                    )
                    ->nullable()
                    ->index();

                $table
                    ->timestamp(
                        'ends_at'
                    )
                    ->nullable()
                    ->index();

                $table->timestamps();
                $table->softDeletes();

                $table->index([
                    'placement',
                    'active',
                    'sort_order',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists(
            'website_ads'
        );
    }
};