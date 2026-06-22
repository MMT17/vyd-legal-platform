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
        Schema::table('querellas', function (Blueprint $table) {
            $table->string('nis')->nullable()->index();
            $table->date('fecha_cierre')->nullable();
            $table->string('tribunal', 100)->nullable();
            $table->string('tipo_proceso', 100)->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('querellas', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropIndex(['nis']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nis',
                'fecha_cierre',
                'tribunal',
                'tipo_proceso',
                'user_id',
            ]);
        });
    }
};
