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
        Schema::table('team_members', function (Blueprint $table) {
            if (! Schema::hasColumn('team_members', 'short_description')) {
                $table->text('short_description')->nullable()->after('position');
            }

            if (! Schema::hasColumn('team_members', 'specialties')) {
                $table->json('specialties')->nullable()->after('short_description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            if (Schema::hasColumn('team_members', 'specialties')) {
                $table->dropColumn('specialties');
            }

            if (Schema::hasColumn('team_members', 'short_description')) {
                $table->dropColumn('short_description');
            }
        });
    }
};
