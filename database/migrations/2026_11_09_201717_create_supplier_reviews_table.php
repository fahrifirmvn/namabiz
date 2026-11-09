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
        Schema::create('supplier_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supplier_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->unsignedTinyInteger('quality_rating')->default(5);
            $table->unsignedTinyInteger('delivery_rating')->default(5);
            $table->unsignedTinyInteger('service_rating')->default(5);
            $table->string('title');
            $table->text('comment');
            $table->string('reviewer_company')->nullable();
            $table->string('reviewer_role')->nullable();
            $table->boolean('is_verified_buyer')->default(false);
            $table->text('supplier_reply')->nullable();
            $table->timestamp('replied_at')->nullable();
            $table->timestamps();

            $table->unique(['supplier_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_reviews');
    }
};
