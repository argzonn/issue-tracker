<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (!Schema::hasColumn('projects', 'start_date')) {
                $table->date('start_date')->nullable()->after('description');
            }
            if (!Schema::hasColumn('projects', 'deadline')) {
                $table->date('deadline')->nullable()->after('start_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'deadline')) {
                $table->dropColumn('deadline');
            }
            if (Schema::hasColumn('projects', 'start_date')) {
                $table->dropColumn('start_date');
            }
        });
    }
};
