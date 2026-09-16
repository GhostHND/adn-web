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
        | SOLICITUDES DE COTIZACIÓN
        |--------------------------------------------------------------------------
        |
        | Esta será la fuente original de toda solicitud realizada desde
        | adnpublicidad.site.
        |
        */

        Schema::create(
            'quote_requests',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('request_number', 50)
                    ->unique();

                /*
                |--------------------------------------------------------------------------
                | Token público que no revela el ID interno.
                |--------------------------------------------------------------------------
                */

                $table
                    ->uuid('public_token')
                    ->unique();

                $table
                    ->string('status', 30)
                    ->default('new')
                    ->index();

                /*
                |--------------------------------------------------------------------------
                | DATOS DEL CLIENTE
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('client_name', 180);

                $table
                    ->string('phone', 50)
                    ->nullable();

                $table
                    ->string('whatsapp', 50)
                    ->nullable();

                $table
                    ->string('email', 180)
                    ->nullable();

                $table
                    ->string('company', 180)
                    ->nullable();

                $table
                    ->text('notes')
                    ->nullable();

                $table
                    ->string('source', 50)
                    ->default('website');

                $table
                    ->boolean('privacy_consent')
                    ->default(false);

                /*
                |--------------------------------------------------------------------------
                | TRAZABILIDAD WEB
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('ip_address', 45)
                    ->nullable();

                $table
                    ->text('user_agent')
                    ->nullable();

                /*
                |--------------------------------------------------------------------------
                | SINCRONIZACIÓN HACIA LA APP
                |--------------------------------------------------------------------------
                |
                | Estos son únicamente IDs de referencia.
                | No son llaves foráneas.
                |
                */

                $table
                    ->string('app_sync_status', 30)
                    ->default('pending')
                    ->index();

                $table
                    ->unsignedBigInteger('app_lead_id')
                    ->nullable();

                $table
                    ->unsignedBigInteger('app_client_id')
                    ->nullable();

                $table
                    ->unsignedInteger('sync_attempts')
                    ->default(0);

                $table
                    ->timestamp('last_sync_at')
                    ->nullable();

                $table
                    ->text('sync_error')
                    ->nullable();

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | PRODUCTOS SOLICITADOS
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'quote_request_items',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('quote_request_id')
                    ->constrained('quote_requests')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('catalog_product_id')
                    ->nullable()
                    ->constrained('catalog_products')
                    ->nullOnDelete();

                /*
                |--------------------------------------------------------------------------
                | SNAPSHOT
                |--------------------------------------------------------------------------
                |
                | Guardamos los datos que vio el cliente en ese momento.
                |
                | Si después editamos el catálogo, la solicitud histórica
                | continuará mostrando la información original.
                |
                */

                $table
                    ->string('product_code_snapshot', 80)
                    ->nullable();

                $table
                    ->string('product_name_snapshot', 220);

                $table
                    ->decimal('quantity', 14, 4)
                    ->default(1);

                $table
                    ->string('unit_snapshot', 60)
                    ->nullable();

                $table
                    ->string('quote_mode_snapshot', 40)
                    ->nullable();

                $table
                    ->decimal('reference_price_snapshot', 14, 2)
                    ->nullable();

                $table
                    ->text('notes')
                    ->nullable();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | VALORES DE LOS CAMPOS DINÁMICOS
        |--------------------------------------------------------------------------
        |
        | Ejemplos:
        |
        | ancho = 36
        | alto = 72
        | talla = XL
        | acabado = Ojales
        |
        */

        Schema::create(
            'quote_request_values',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('quote_request_item_id')
                    ->constrained('quote_request_items')
                    ->cascadeOnDelete();

                $table
                    ->string('field_key', 120);

                $table
                    ->string('label_snapshot', 200);

                $table
                    ->longText('value_text')
                    ->nullable();

                $table
                    ->json('value_json')
                    ->nullable();

                $table
                    ->string('unit_snapshot', 60)
                    ->nullable();

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | ARCHIVOS DEL CLIENTE
        |--------------------------------------------------------------------------
        |
        | Logos
        | Diseños
        | Fotografías
        | PDF
        | Referencias
        |
        */

        Schema::create(
            'quote_request_files',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('quote_request_id')
                    ->constrained('quote_requests')
                    ->cascadeOnDelete();

                $table
                    ->foreignId('quote_request_item_id')
                    ->nullable()
                    ->constrained('quote_request_items')
                    ->cascadeOnDelete();

                $table
                    ->string('path', 500);

                $table
                    ->string('original_name', 255);

                $table
                    ->string('mime_type', 150)
                    ->nullable();

                $table
                    ->unsignedBigInteger('size')
                    ->nullable();

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | LOG DE INTEGRACIÓN WEB → APP
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'integration_logs',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('quote_request_id')
                    ->nullable()
                    ->constrained('quote_requests')
                    ->nullOnDelete();

                $table
                    ->string('direction', 40)
                    ->default('web_to_app');

                $table
                    ->string('event', 100);

                $table
                    ->string('status', 30)
                    ->default('pending')
                    ->index();

                /*
                |--------------------------------------------------------------------------
                | Evitar duplicados.
                |--------------------------------------------------------------------------
                */

                $table
                    ->string('idempotency_key', 150)
                    ->nullable()
                    ->unique();

                $table
                    ->json('request_payload')
                    ->nullable();

                $table
                    ->json('response_payload')
                    ->nullable();

                $table
                    ->unsignedSmallInteger('http_status')
                    ->nullable();

                $table
                    ->text('error_message')
                    ->nullable();

                $table
                    ->timestamp('attempted_at')
                    ->nullable();

                $table
                    ->timestamp('completed_at')
                    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
        Schema::dropIfExists('quote_request_files');
        Schema::dropIfExists('quote_request_values');
        Schema::dropIfExists('quote_request_items');
        Schema::dropIfExists('quote_requests');
    }
};