<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->foreignId('operation_workflow_node_id')->nullable()->after('stage_id')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('document_requests', function (Blueprint $table) {
            $table->dropForeign(['operation_workflow_node_id']);
            $table->dropColumn('operation_workflow_node_id');
        });
    }
};
