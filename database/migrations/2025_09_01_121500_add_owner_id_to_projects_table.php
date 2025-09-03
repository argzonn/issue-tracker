<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Add the column if missing
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'owner_id')) {
                // Keep it nullable so backfill can run safely; you can enforce NOT NULL later if you want
                $table->unsignedBigInteger('owner_id')->nullable()->after('deadline');
                $table->index('owner_id', 'projects_owner_id_index');
                // FK is optional for SQLite; if you’re on MySQL/Postgres this will work:
                // $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            }
        });

        // 2) Backfill from user_id if that legacy column exists
        if (Schema::hasColumn('projects', 'user_id')) {
            DB::table('projects')
                ->whereNull('owner_id')
                ->update(['owner_id' => DB::raw('user_id')]);
        }
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'owner_id')) {
                // If you added a FK above on a non-SQLite DB, drop it first:
                // $table->dropForeign(['owner_id']);
                $table->dropIndex('projects_owner_id_index');
                $table->dropColumn('owner_id');
            }
        });
    }
};
