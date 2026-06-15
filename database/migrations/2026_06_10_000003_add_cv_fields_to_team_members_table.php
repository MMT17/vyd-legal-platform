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
            if (! Schema::hasColumn('team_members', 'education')) {
                $table->json('education')->nullable()->after('specialties');
            }

            if (! Schema::hasColumn('team_members', 'experience')) {
                $table->json('experience')->nullable()->after('education');
            }

            if (! Schema::hasColumn('team_members', 'activities')) {
                $table->json('activities')->nullable()->after('experience');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            if (Schema::hasColumn('team_members', 'activities')) {
                $table->dropColumn('activities');
            }

            if (Schema::hasColumn('team_members', 'experience')) {
                $table->dropColumn('experience');
            }

            if (Schema::hasColumn('team_members', 'education')) {
                $table->dropColumn('education');
            }
        });
    }
};
