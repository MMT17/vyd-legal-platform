<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('importacion_historial', function (Blueprint $table) {
            if (! Schema::hasColumn('importacion_historial', 'archivo_almacenado')) {
                $table->string('archivo_almacenado')->nullable()->after('archivo_original');
            }

            if (! Schema::hasColumn('importacion_historial', 'registros_validos')) {
                $table->integer('registros_validos')->default(0)->after('total_registros');
            }

            if (! Schema::hasColumn('importacion_historial', 'reporte_errores')) {
                $table->string('reporte_errores')->nullable()->after('errores');
            }

            if (! Schema::hasColumn('importacion_historial', 'iniciado_at')) {
                $table->dateTime('iniciado_at')->nullable()->after('detalles');
            }

            if (! Schema::hasColumn('importacion_historial', 'completado_at')) {
                $table->dateTime('completado_at')->nullable()->after('iniciado_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('importacion_historial', function (Blueprint $table) {
            foreach ([
                'archivo_almacenado',
                'registros_validos',
                'reporte_errores',
                'iniciado_at',
                'completado_at',
            ] as $column) {
                if (Schema::hasColumn('importacion_historial', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
