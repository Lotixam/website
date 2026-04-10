<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 64)->nullable();
            $table->text('message');
            $table->string('source_page', 64)->nullable();
            $table->timestamps();
        });

        Schema::create('contact_submission_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_submission_id')->constrained('contact_submissions')->cascadeOnDelete();
            $table->string('disk', 32)->default('local');
            $table->string('path');
            $table->string('original_name');
            $table->string('mime_type', 127)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_submission_attachments');
        Schema::dropIfExists('contact_submissions');
    }
};
