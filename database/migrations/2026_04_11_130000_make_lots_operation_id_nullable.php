<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropForeign(['operation_id']);
        });

        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_id')->nullable()->change();
        });

        Schema::table('lots', function (Blueprint $table) {
            $table->foreign('operation_id')
                ->references('id')
                ->on('operations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lots', function (Blueprint $table) {
            $table->dropForeign(['operation_id']);
        });

        Schema::table('lots', function (Blueprint $table) {
            $table->unsignedBigInteger('operation_id')->nullable(false)->change();
        });

        Schema::table('lots', function (Blueprint $table) {
            $table->foreign('operation_id')
                ->references('id')
                ->on('operations')
                ->cascadeOnDelete();
        });
    }
};
