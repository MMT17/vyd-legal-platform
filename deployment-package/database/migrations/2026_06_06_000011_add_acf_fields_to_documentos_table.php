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
        Schema::table('documentos', function (Blueprint $table) {
            $table->string('tipo_documento', 100)->nullable();
            $table->string('checksum_archivo', 64)->nullable();
            $table->unsignedBigInteger('tamano_archivo')->nullable();
            $table->string('mime_type', 100)->nullable();
            $table->string('nombre_original')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentos', function (Blueprint $table) {
            $table->dropColumn([
                'tipo_documento',
                'checksum_archivo',
                'tamano_archivo',
                'mime_type',
                'nombre_original',
            ]);
        });
    }
};
