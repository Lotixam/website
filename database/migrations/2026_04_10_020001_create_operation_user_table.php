<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operation_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('client');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            $table->unique(['operation_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_user');
    }
};
