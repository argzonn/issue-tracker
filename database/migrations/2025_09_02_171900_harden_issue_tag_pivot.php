<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('issue_tag')) {
            return;
        }

        // 1) De-duplicate existing rows so the unique index can be created safely
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // Keep the oldest row per (issue_id, tag_id)
            DB::statement("
                DELETE FROM issue_tag
                WHERE rowid NOT IN (
                    SELECT MIN(rowid) FROM issue_tag GROUP BY issue_id, tag_id
                )
            ");
        } else {
            // MySQL/Postgres
            DB::statement("
                DELETE it1 FROM issue_tag it1
                JOIN issue_tag it2
                  ON it1.issue_id = it2.issue_id
                 AND it1.tag_id  = it2.tag_id
                 AND it1.id      > it2.id
            ");
        }

        // 2) Add a composite UNIQUE constraint so each tag can be attached once per issue
        Schema::table('issue_tag', function (Blueprint $table) {
            // Name it explicitly so down() can drop it reliably
            $table->unique(['issue_id','tag_id'], 'issue_tag_issue_id_tag_id_unique');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('issue_tag')) {
            return;
        }

        Schema::table('issue_tag', function (Blueprint $table) {
            $table->dropUnique('issue_tag_issue_id_tag_id_unique');
        });
    }
};
