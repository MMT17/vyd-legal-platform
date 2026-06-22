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
        Schema::create('importacion_historial', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tipo_importacion', 50)->index();
            $table->string('archivo_original')->nullable();
            $table->integer('total_registros')->default(0);
            $table->integer('creados')->default(0);
            $table->integer('actualizados')->default(0);
            $table->integer('duplicados')->default(0);
            $table->integer('errores')->default(0);
            $table->string('estado', 50)->default('pendiente');
            $table->json('detalles')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('importacion_historial');
    }
};
