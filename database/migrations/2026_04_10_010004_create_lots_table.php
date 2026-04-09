<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->cascadeOnDelete();
            $table->string('lot_number');
            $table->decimal('surface', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->decimal('selling_price', 12, 2)->nullable();
            $table->string('status')->default('available');
            $table->foreignId('buyer_contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->date('sold_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lots');
    }
};
