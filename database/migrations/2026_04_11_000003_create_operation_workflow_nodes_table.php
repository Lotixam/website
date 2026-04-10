<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_workflow_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workflow_template_node_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('operation_workflow_nodes')->cascadeOnDelete();
            $table->string('parallel_group')->nullable()->index();
            $table->boolean('is_merge_node')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('validation_policy')->default('lotixam_only');
            $table->string('participant_visibility')->default('all_assigned');
            $table->string('status')->default('pending');
            $table->foreignId('blocked_by_node_id')->nullable()->constrained('operation_workflow_nodes')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_workflow_nodes');
    }
};
