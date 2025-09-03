<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Backfill owner_id from legacy user_id when missing
        if (Schema::hasColumn('projects', 'owner_id') && Schema::hasColumn('projects', 'user_id')) {
            DB::table('projects')
                ->whereNull('owner_id')
                ->update(['owner_id' => DB::raw('user_id')]);
        }
    }

    public function down(): void
    {
        // No-op: we won't un-backfill
    }
};
