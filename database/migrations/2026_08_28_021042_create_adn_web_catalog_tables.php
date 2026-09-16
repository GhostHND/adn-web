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
        | CATEGORÍAS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'catalog_categories',
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
                    ->text('description')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Subcategorías
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('parent_id')
                    ->nullable()
                    ->constrained('catalog_categories')
                    ->nullOnDelete();

                $table
                    ->foreignId('media_id')
                    ->nullable()
                    ->constrained('media')
                    ->nullOnDelete();

                $table
                    ->string('status', 30)
                    ->default('published')
                    ->index();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

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
        | PRODUCTOS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'catalog_products',
            function (Blueprint $table): void {
                $table->id();

                /*
                |--------------------------------------------------------------------------
                | Código propio del catálogo WEB.
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('code', 50)
                    ->unique();

                /*
                |--------------------------------------------------------------------------
                | Referencia opcional al catálogo de la APP.
                |
                | Ejemplo:
                |
                | WEB-0001
                |     ↓
                | REV-002
                |
                | No existe FK entre las dos bases.
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('app_reference_code', 80)
                    ->nullable()
                    ->index();

                $table
                    ->foreignId('category_id')
                    ->nullable()
                    ->constrained('catalog_categories')
                    ->nullOnDelete();

                $table
                    ->string('slug', 180)
                    ->unique();

                $table
                    ->string('name', 200);

                $table
                    ->string('short_description', 500)
                    ->nullable();

                $table
                    ->longText('description')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Características comerciales.
                |--------------------------------------------------------------------------
                */

                $table
                    ->json('features')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | Imagen principal.
                |--------------------------------------------------------------------------
                */

                $table
                    ->foreignId('main_media_id')
                    ->nullable()
                    ->constrained('media')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | PRECIO PÚBLICO OPCIONAL
                |--------------------------------------------------------------------------
                |
                | El costo interno de ADN NO existe aquí.
                |
                */

                $table
                    ->boolean('price_visible')
                    ->default(false);

                $table
                    ->boolean('price_from')
                    ->default(false);

                $table
                    ->decimal('reference_price', 14, 2)
                    ->nullable();

                $table
                    ->string('price_note', 255)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | COTIZACIÓN
                |--------------------------------------------------------------------------
                |
                | Ejemplos de quote_mode:
                |
                | UNIT
                | AREA
                | LINEAR
                | CUSTOM
                |
                */

                $table
                    ->string('quote_mode', 30)
                    ->default('CUSTOM');

                $table
                    ->string('measurement_unit', 50)
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | PUBLICACIÓN
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('status', 30)
                    ->default('draft')
                    ->index();

                $table
                    ->boolean('featured')
                    ->default(false);

                $table
                    ->boolean('show_on_home')
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
        | GALERÍA DEL PRODUCTO
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'catalog_product_images',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('catalog_product_id')
                    ->constrained('catalog_products')
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
                    'catalog_product_id',
                    'media_id',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | CAMPOS DINÁMICOS DE COTIZACIÓN
        |--------------------------------------------------------------------------
        |
        | Esto permitirá que desde el Editor podamos configurar:
        |
        | Cantidad
        | Ancho
        | Alto
        | Largo
        | Color
        | Talla
        | Material
        | Acabado
        | Archivo
        | Texto
        | Select
        | Radio
        | Checkbox
        | etc.
        |
        */

        Schema::create(
            'catalog_product_fields',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('catalog_product_id')
                    ->constrained('catalog_products')
                    ->cascadeOnDelete();

                $table
                    ->string('field_key', 100);

                $table
                    ->string('label', 180);

                $table
                    ->string('field_type', 40);

                $table
                    ->string('placeholder', 255)
                    ->nullable();

                $table
                    ->text('help_text')
                    ->nullable();

                $table
                    ->string('unit', 50)
                    ->nullable();

                $table
                    ->boolean('required')
                    ->default(false);

                $table
                    ->decimal('min_value', 14, 4)
                    ->nullable();

                $table
                    ->decimal('max_value', 14, 4)
                    ->nullable();

                $table
                    ->decimal('step', 14, 4)
                    ->nullable();

                $table
                    ->string('default_value', 500)
                    ->nullable();

                $table
                    ->json('validation_rules')
                    ->nullable();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table
                    ->boolean('active')
                    ->default(true);

                $table->timestamps();

                $table->unique([
                    'catalog_product_id',
                    'field_key',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | OPCIONES DE CAMPOS
        |--------------------------------------------------------------------------
        |
        | Ejemplo:
        |
        | Campo: talla
        |
        | S
        | M
        | L
        | XL
        |
        */

        Schema::create(
            'catalog_product_options',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('catalog_product_field_id')
                    ->constrained('catalog_product_fields')
                    ->cascadeOnDelete();

                $table
                    ->string('value', 180);

                $table
                    ->string('label', 180);

                /*
                |--------------------------------------------------------------------------
                | Diferencia pública opcional.
                |--------------------------------------------------------------------------
                */

                $table
                    ->decimal('extra_price', 14, 2)
                    ->default(0);

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table
                    ->boolean('active')
                    ->default(true);

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_product_options');
        Schema::dropIfExists('catalog_product_fields');
        Schema::dropIfExists('catalog_product_images');
        Schema::dropIfExists('catalog_products');
        Schema::dropIfExists('catalog_categories');
    }
};