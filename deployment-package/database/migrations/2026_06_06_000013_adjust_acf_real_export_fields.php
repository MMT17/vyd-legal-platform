<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            if (! Schema::hasColumn('convenios', 'nis_convenio')) {
                $table->string('nis_convenio')->nullable()->index();
            }

            if (! Schema::hasColumn('convenios', 'id_usuario')) {
                $table->string('id_usuario')->nullable();
            }

            if (Schema::hasColumn('convenios', 'fecha_cierre')) {
                $table->dateTime('fecha_cierre')->nullable()->change();
            } else {
                $table->dateTime('fecha_cierre')->nullable();
            }

            if (! Schema::hasColumn('convenios', 'documentacion_estado')) {
                $table->string('documentacion_estado', 100)->nullable();
            }

            foreach ([
                'cnr_12_meses',
                'cnr_fuera_ventana',
                'deuda_total',
                'acuerdo_extrajudicial',
                'cnr_pagado_anterior',
            ] as $column) {
                if (Schema::hasColumn('convenios', $column)) {
                    $table->decimal($column, 12, 2)->nullable()->change();
                } else {
                    $table->decimal($column, 12, 2)->nullable();
                }
            }

            if (! Schema::hasColumn('convenios', 'cuotas')) {
                $table->integer('cuotas')->nullable();
            }

            if (! Schema::hasColumn('convenios', 'caso')) {
                $table->string('caso')->nullable();
            }

            if (! Schema::hasColumn('convenios', 'nombre_abogado')) {
                $table->string('nombre_abogado', 100)->nullable();
            }

            if (! Schema::hasColumn('convenios', 'abogado_responsable')) {
                $table->string('abogado_responsable')->nullable();
            }

            if (! Schema::hasColumn('convenios', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('convenios', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('querellas', function (Blueprint $table) {
            if (! Schema::hasColumn('querellas', 'id_querella')) {
                $table->string('id_querella')->nullable()->index();
            }

            if (Schema::hasColumn('querellas', 'fecha_cierre')) {
                $table->dateTime('fecha_cierre')->nullable()->change();
            } else {
                $table->dateTime('fecha_cierre')->nullable();
            }

            if (! Schema::hasColumn('querellas', 'documentacion_estado')) {
                $table->string('documentacion_estado', 100)->nullable();
            }

            foreach ([
                'cnr_12_meses',
                'cnr_fuera_ventana',
                'deuda_total',
                'acuerdo_extrajudicial',
                'cnr_pagado_anterior',
            ] as $column) {
                if (Schema::hasColumn('querellas', $column)) {
                    $table->decimal($column, 12, 2)->nullable()->change();
                } else {
                    $table->decimal($column, 12, 2)->nullable();
                }
            }

            if (! Schema::hasColumn('querellas', 'cuotas')) {
                $table->integer('cuotas')->nullable();
            }

            if (! Schema::hasColumn('querellas', 'abogado_responsable')) {
                $table->string('abogado_responsable')->nullable();
            }

            if (! Schema::hasColumn('querellas', 'tribunal')) {
                $table->string('tribunal', 100)->nullable();
            }

            if (! Schema::hasColumn('querellas', 'tipo_proceso')) {
                $table->string('tipo_proceso', 100)->nullable();
            }

            if (! Schema::hasColumn('querellas', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('querellas', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('contactos', function (Blueprint $table) {
            if (! Schema::hasColumn('contactos', 'user_id')) {
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            }
        });

        Schema::table('documentos', function (Blueprint $table) {
            if (! Schema::hasColumn('documentos', 'tipo_documento')) {
                $table->string('tipo_documento', 100)->nullable();
            }

            if (! Schema::hasColumn('documentos', 'checksum_archivo')) {
                $table->string('checksum_archivo', 64)->nullable();
            }

            if (! Schema::hasColumn('documentos', 'tamano_archivo')) {
                $table->unsignedBigInteger('tamano_archivo')->nullable();
            }

            if (! Schema::hasColumn('documentos', 'mime_type')) {
                $table->string('mime_type', 100)->nullable();
            }

            if (! Schema::hasColumn('documentos', 'nombre_original')) {
                $table->string('nombre_original')->nullable();
            }
        });

        Schema::table('procesos', function (Blueprint $table) {
            if (! Schema::hasColumn('procesos', 'checksum_archivo')) {
                $table->string('checksum_archivo', 64)->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropColumn([
                'nis_convenio',
                'id_usuario',
                'cuotas',
                'abogado_responsable',
            ]);
        });

        Schema::table('querellas', function (Blueprint $table) {
            $table->dropColumn([
                'id_querella',
                'documentacion_estado',
                'cnr_12_meses',
                'cnr_fuera_ventana',
                'deuda_total',
                'acuerdo_extrajudicial',
                'cuotas',
                'cnr_pagado_anterior',
                'abogado_responsable',
            ]);
        });
    }
};
