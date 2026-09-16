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
        | USUARIOS DEL EDITOR
        |--------------------------------------------------------------------------
        |
        | El editor tendrá sus propios usuarios/sesiones.
        |
        | app_user_id permitirá asociar opcionalmente un usuario del editor
        | con el usuario que ingresó desde app.adnpublicidad.site.
        |
        | NO es llave foránea porque pertenece a otra base de datos.
        |
        */

        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table
                    ->unsignedBigInteger('app_user_id')
                    ->nullable()
                    ->unique()
                    ->after('id');

                $table
                    ->string('role', 40)
                    ->default('editor')
                    ->after('password');

                $table
                    ->boolean('active')
                    ->default(true)
                    ->after('role');

                $table
                    ->timestamp('last_login_at')
                    ->nullable()
                    ->after('active');

                $table
                    ->timestamp('last_seen_at')
                    ->nullable()
                    ->after('last_login_at');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | CONFIGURACIÓN DEL SITIO
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'settings',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->string('group', 80)
                    ->default('general')
                    ->index();

                $table
                    ->string('key', 150)
                    ->unique();

                $table
                    ->longText('value')
                    ->nullable();

                $table
                    ->string('type', 30)
                    ->default('text');

                $table
                    ->boolean('is_public')
                    ->default(false);

                $table
                    ->unsignedInteger('sort_order')
                    ->default(0);

                $table->timestamps();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | BIBLIOTECA MULTIMEDIA
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'media',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->uuid('uuid')
                    ->unique();

                $table
                    ->string('disk', 50)
                    ->default('public');

                $table
                    ->string('path', 500);

                $table
                    ->string('filename', 255);

                $table
                    ->string('original_name', 255)
                    ->nullable();

                $table
                    ->string('mime_type', 150)
                    ->nullable();

                $table
                    ->string('extension', 20)
                    ->nullable();

                $table
                    ->string('title', 255)
                    ->nullable();

                $table
                    ->string('alt_text', 255)
                    ->nullable();

                $table
                    ->text('caption')
                    ->nullable();

                $table
                    ->unsignedInteger('width')
                    ->nullable();

                $table
                    ->unsignedInteger('height')
                    ->nullable();

                $table
                    ->unsignedBigInteger('size')
                    ->nullable();

                $table
                    ->foreignId('uploaded_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->boolean('is_public')
                    ->default(true);

                $table->timestamps();
                $table->softDeletes();
            }
        );

        /*
        |--------------------------------------------------------------------------
        | AUDITORÍA DEL EDITOR
        |--------------------------------------------------------------------------
        */

        Schema::create(
            'audit_logs',
            function (Blueprint $table): void {
                $table->id();

                $table
                    ->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table
                    ->string('action', 80)
                    ->index();

                $table
                    ->string('entity_type', 150)
                    ->nullable()
                    ->index();

                $table
                    ->unsignedBigInteger('entity_id')
                    ->nullable()
                    ->index();

                $table
                    ->text('description')
                    ->nullable();

                $table
                    ->json('old_values')
                    ->nullable();

                $table
                    ->json('new_values')
                    ->nullable();

                $table
                    ->string('ip_address', 45)
                    ->nullable();

                $table
                    ->text('user_agent')
                    ->nullable();

                $table->timestamps();
            }
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('media');
        Schema::dropIfExists('settings');

        Schema::table(
            'users',
            function (Blueprint $table): void {
                $table->dropColumn([
                    'app_user_id',
                    'role',
                    'active',
                    'last_login_at',
                    'last_seen_at',
                ]);
            }
        );
    }
};