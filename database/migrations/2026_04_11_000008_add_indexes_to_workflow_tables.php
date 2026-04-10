<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operation_workflow_nodes', function (Blueprint $table) {
            $table->index(['operation_id', 'parent_id', 'sort_order'], 'own_op_parent_sort_idx');
            $table->index(['operation_id', 'status'], 'own_op_status_idx');
        });

        Schema::table('document_requests', function (Blueprint $table) {
            $table->index(['operation_workflow_node_id', 'status'], 'dr_own_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropIndex('dr_own_status_idx');
        });

        Schema::table('operation_workflow_nodes', function (Blueprint $table) {
            $table->dropIndex('own_op_parent_sort_idx');
            $table->dropIndex('own_op_status_idx');
        });
    }
};
