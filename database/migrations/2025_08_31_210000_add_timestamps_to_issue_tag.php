<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('issue_tag', function (Blueprint $table) {
            if (!Schema::hasColumn('issue_tag', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('issue_tag', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('issue_tag', function (Blueprint $table) {
            if (Schema::hasColumn('issue_tag', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
            if (Schema::hasColumn('issue_tag', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
