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
       Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->string('return_number')->unique();
            $table->enum('reason', [
                'too_small','too_large','wrong_item','damaged',
                'quality_issue','not_as_described','other'
            ]);
            $table->text('detailed_reason')->nullable();
            $table->string('returned_size');
            $table->string('preferred_replacement_size')->nullable();
            $table->enum('status', ['requested','approved','rejected','processing','completed'])
                ->default('requested');
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
