<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_metrics', function (Blueprint $table): void {
            $table->id();
            $table->string('label');
            $table->string('source', 64)->default('manual');
            $table->unsignedInteger('value_override')->nullable();
            $table->string('suffix', 20)->default('+');
            $table->boolean('is_visible')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_metrics');
    }
};
