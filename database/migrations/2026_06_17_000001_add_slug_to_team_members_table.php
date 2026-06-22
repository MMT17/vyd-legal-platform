<?php

use App\Models\Cms\TeamMember;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $addedSlugColumn = ! Schema::hasColumn('team_members', 'slug');

        Schema::table('team_members', function (Blueprint $table) {
            if (! Schema::hasColumn('team_members', 'slug')) {
                $table->string('slug')->nullable()->after('name');
            }
        });

        TeamMember::query()
            ->whereNull('slug')
            ->orWhere('slug', '')
            ->orderBy('id')
            ->get()
            ->each(function (TeamMember $member): void {
                $baseSlug = Str::slug($member->name);
                $slug = $baseSlug;
                $counter = 2;

                while (
                    TeamMember::query()
                        ->where('slug', $slug)
                        ->whereKeyNot($member->getKey())
                        ->exists()
                ) {
                    $slug = "{$baseSlug}-{$counter}";
                    $counter++;
                }

                $member->forceFill(['slug' => $slug])->save();
            });

        Schema::table('team_members', function (Blueprint $table) use ($addedSlugColumn) {
            if ($addedSlugColumn && Schema::hasColumn('team_members', 'slug')) {
                $table->string('slug')->nullable(false)->change();
                $table->unique('slug');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('team_members', function (Blueprint $table) {
            if (Schema::hasColumn('team_members', 'slug')) {
                $table->dropUnique(['slug']);
                $table->dropColumn('slug');
            }
        });
    }
};
