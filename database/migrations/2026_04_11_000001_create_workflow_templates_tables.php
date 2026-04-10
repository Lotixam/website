<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('workflow_template_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workflow_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('workflow_template_nodes')->cascadeOnDelete();
            $table->string('parallel_group')->nullable()->index();
            $table->boolean('is_merge_node')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('validation_policy')->default('lotixam_only');
            $table->string('participant_visibility')->default('all_assigned');
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_template_nodes');
        Schema::dropIfExists('workflow_templates');
    }
};
