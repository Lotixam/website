<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->foreignId('parent_operation_id')->nullable()->after('id')->constrained('operations')->nullOnDelete();
            $table->foreignId('workflow_template_id')->nullable()->after('parent_operation_id')->constrained('workflow_templates')->nullOnDelete();
            $table->text('internal_objective')->nullable()->after('notes');
            $table->string('participant_label')->nullable()->after('internal_objective');
            $table->string('mission')->nullable()->after('participant_label');
            $table->timestamp('closed_at')->nullable()->after('mission');
            $table->foreignId('closed_by_user_id')->nullable()->after('closed_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['parent_operation_id']);
            $table->dropForeign(['workflow_template_id']);
            $table->dropForeign(['closed_by_user_id']);
            $table->dropColumn([
                'parent_operation_id',
                'workflow_template_id',
                'internal_objective',
                'participant_label',
                'mission',
                'closed_at',
                'closed_by_user_id',
            ]);
        });
    }
};
