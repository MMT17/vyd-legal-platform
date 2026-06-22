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
            $table->string('nis')->nullable()->index();
            $table->date('fecha_cierre')->nullable();
            $table->string('documentacion_estado', 100)->nullable();
            $table->string('cnr_12_meses')->nullable();
            $table->string('cnr_fuera_ventana')->nullable();
            $table->decimal('deuda_total', 12, 2)->nullable();
            $table->string('acuerdo_extrajudicial', 100)->nullable();
            $table->string('cnr_pagado_anterior')->nullable();
            $table->string('caso')->nullable();
            $table->string('nombre_abogado', 100)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['nis']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nis',
                'fecha_cierre',
                'documentacion_estado',
                'cnr_12_meses',
                'cnr_fuera_ventana',
                'deuda_total',
                'acuerdo_extrajudicial',
                'cnr_pagado_anterior',
                'caso',
                'nombre_abogado',
                'user_id',
            ]);
        });
    }
};
