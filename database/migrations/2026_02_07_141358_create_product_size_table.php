<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('product_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('size');
            $table->integer('stock_quantity')->default(0);
            $table->decimal('chest', 5, 2)->nullable();
            $table->decimal('waist', 5, 2)->nullable();
            $table->decimal('hip', 5, 2)->nullable();
            $table->decimal('length', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['product_id', 'size']);
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_size');
    }
};
