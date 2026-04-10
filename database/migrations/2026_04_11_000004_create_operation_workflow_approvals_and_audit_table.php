<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_workflow_approvals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_workflow_node_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('actor_role')->nullable();
            $table->string('state')->default('pending');
            $table->text('comment')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->index(['operation_workflow_node_id', 'state'], 'owa_node_state_idx');
        });

        Schema::create('workflow_audit_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type');
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['operation_id', 'created_at'], 'wae_op_created_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_audit_events');
        Schema::dropIfExists('operation_workflow_approvals');
    }
};
