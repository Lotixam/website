<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->string('author_first_name', 120)->nullable()->after('excerpt');
            $table->string('author_last_name', 120)->nullable()->after('author_first_name');
        });
    }

    public function down(): void
    {
        Schema::table('blog_posts', function (Blueprint $table): void {
            $table->dropColumn(['author_first_name', 'author_last_name']);
        });
    }
};
