<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operation_stage', function (Blueprint $table) {
            $table->foreignId('added_by_user_id')->nullable()->after('notes')->constrained('users')->nullOnDelete();
            $table->string('source')->default('default')->after('added_by_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('operation_stage', function (Blueprint $table) {
            $table->dropForeign(['added_by_user_id']);
            $table->dropColumn(['added_by_user_id', 'source']);
        });
    }
};
