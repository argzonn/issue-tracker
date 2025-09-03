<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('issues', function (Blueprint $table) {
            $table->index('project_id');
            $table->index('status');
            $table->index('priority');
            $table->index('due_date');
        });
    }
    public function down(): void {
        Schema::table('issues', function (Blueprint $table) {
            $table->dropIndex(['project_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['due_date']);
        });
    }
};
