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
        | PÁGINAS
        |--------------------------------------------------------------------------
        |
        | Inicio
        | Sobre nosotros
        | Servicios
        | Catálogo
        | Portafolio
        | Contacto
        |
        */

        Schema::create(
            'pages',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('slug', 150)
                    ->unique();

                $table
                    ->string('name', 150);

                $table
                    ->string('title', 255);

                $table
                    ->string('eyebrow', 255)
                    ->nullable();

                $table
                    ->text('summary')
                    ->nullable();

                $table
                    ->string('status', 30)
                    ->default('draft')
                    ->index();

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

                $table
                    ->string('canonical_url', 500)
                    ->nullable();

                $table
                    ->foreignId('og_media_id')
                    ->nullable()
                    ->constrained('media')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | PUBLICACIÓN
                |--------------------------------------------------------------------------
                */

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table
                    ->timestamp('published_at')
                    ->nullable();

                $table->timestamps();
                $table->softDeletes();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | SECCIONES EDITABLES DE LAS PÁGINAS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'page_sections',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('page_id')
                    ->constrained('pages')
                    ->cascadeOnDelete();

                /*
                |--------------------------------------------------------------------------
                | Ejemplos:
                |
                | hero
                | introduction
                | services
                | featured_products
                | portfolio
                | call_to_action
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('section_key', 120);

                $table
                    ->string('section_type', 80)
                    ->default('content');

                $table
                    ->string('title', 255)
                    ->nullable();

                $table
                    ->string('subtitle', 255)
                    ->nullable();

                $table
                    ->longText('body')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Contenido adicional configurable.
                |--------------------------------------------------------------------------
                */

                $table
                    ->json('content')
                    ->nullable();

                $table
                    ->foreignId('media_id')
                    ->nullable()
                    ->constrained('media')
                    ->nullOnDelete();

                $table
                    ->json('settings')
                    ->nullable();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table
                    ->boolean('active')
                    ->default(true);

                $table->timestamps();
                $table->softDeletes();

                $table->unique([
                    'page_id',
                    'section_key',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | SERVICIOS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'services',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('code', 40)
                    ->unique();

                $table
                    ->string('slug', 160)
                    ->unique();

                $table
                    ->string('name', 180);

                $table
                    ->string('short_description', 500)
                    ->nullable();

                $table
                    ->longText('description')
                    ->nullable();

                $table
                    ->string('icon', 120)
                    ->nullable();

                $table
                    ->foreignId('main_media_id')
                    ->nullable()
                    ->constrained('media')
                    ->nullOnDelete();

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
        | CARACTERÍSTICAS DE SERVICIO
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'service_features',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('service_id')
                    ->constrained('services')
                    ->cascadeOnDelete();

                $table
                    ->string('title', 255);

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
            }
        );

        /*
        |--------------------------------------------------------------------------
        | GALERÍA DE SERVICIOS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'service_images',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('service_id')
                    ->constrained('services')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('media_id')
                    ->constrained('media')
                    ->cascadeOnDelete();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table->timestamps();

                $table->unique([
                    'service_id',
                    'media_id',
                ]);
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('service_images');
        Schema::dropIfExists('service_features');
        Schema::dropIfExists('services');
        Schema::dropIfExists('page_sections');
        Schema::dropIfExists('pages');
    }
};