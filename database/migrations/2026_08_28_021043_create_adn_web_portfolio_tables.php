<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CATEGORÍAS DEL PORTAFOLIO
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'portfolio_categories',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('slug', 160)
                    ->unique();

                $table
                    ->string('name', 180);

                $table
                    ->text('description')
                    ->nullable();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table
                    ->boolean('active')
                    ->default(true);

                $table->timestamps();
                $table->softDeletes();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | PROYECTOS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'portfolio_projects',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('code', 50)
                    ->unique();

                $table
                    ->string('slug', 180)
                    ->unique();

                $table
                    ->string('title', 220);

                $table
                    ->string('client_name', 180)
                    ->nullable();

                $table
                    ->string('excerpt', 500)
                    ->nullable();

                $table
                    ->longText('description')
                    ->nullable();

                $table
                    ->foreignId('category_id')
                    ->nullable()
                    ->constrained('portfolio_categories')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Relaciones opcionales.
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('service_id')
                    ->nullable()
                    ->constrained('services')
                    ->nullOnDelete();

                $table
                    ->foreignId('catalog_product_id')
                    ->nullable()
                    ->constrained('catalog_products')
                    ->nullOnDelete();

                $table
                    ->foreignId('main_media_id')
                    ->nullable()
                    ->constrained('media')
                    ->nullOnDelete();

                $table
                    ->date('project_date')
                    ->nullable();

                $table
                    ->string('status', 30)
                    ->default('draft')
                    ->index();

                $table
                    ->boolean('featured')
                    ->default(false);

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                /*
                |--------------------------------------------------------------------------
                | SEO
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('meta_title', 255)
                    ->nullable();

                $table
                    ->text('meta_description')
                    ->nullable();

                $table->timestamps();
                $table->softDeletes();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | GALERÍA DEL PROYECTO
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'portfolio_images',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('portfolio_project_id')
                    ->constrained('portfolio_projects')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('media_id')
                    ->constrained('media')
                    ->cascadeOnDelete();

                $table
                    ->string('alt_text', 255)
                    ->nullable();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table->timestamps();

                $table->unique([
                    'portfolio_project_id',
                    'media_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_images');
        Schema::dropIfExists('portfolio_projects');
        Schema::dropIfExists('portfolio_categories');
    }
};