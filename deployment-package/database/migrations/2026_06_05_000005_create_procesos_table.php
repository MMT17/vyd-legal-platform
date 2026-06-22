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
        Schema::create('procesos', function (Blueprint $table) {
            $table->id();
            $table->morphs('procesable');
            $table->string('nombre')->nullable();
            $table->string('tipo')->nullable();
            $table->dateTime('fecha')->nullable();
            $table->text('descripcion')->nullable();
            $table->string('archivo_path')->nullable();
            $table->unsignedBigInteger('wordpress_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procesos');
    }
};
