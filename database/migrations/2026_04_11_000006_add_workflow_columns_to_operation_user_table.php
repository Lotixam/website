<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operation_user', function (Blueprint $table) {
            $table->foreignId('workflow_entry_node_id')->nullable()->after('assigned_at')->constrained('operation_workflow_nodes')->nullOnDelete();
            $table->boolean('hide_upstream_steps')->default(true)->after('workflow_entry_node_id');
            $table->string('participant_kind')->nullable()->after('hide_upstream_steps');
        });
    }

    public function down(): void
    {
        Schema::table('operation_user', function (Blueprint $table) {
            $table->dropForeign(['workflow_entry_node_id']);
            $table->dropColumn(['workflow_entry_node_id', 'hide_upstream_steps', 'participant_kind']);
        });
    }
};
