<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('issue_user')) {
            // Table already exists; do nothing
            return;
        }

        Schema::create('issue_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('issue_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->timestamps();
    $table->unique(['issue_id','user_id']);
});
    }

    public function down(): void
    {
        Schema::dropIfExists('issue_user');
    }
};
