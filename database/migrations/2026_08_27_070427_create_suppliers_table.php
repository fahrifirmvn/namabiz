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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo')->nullable();
            $table->string('tagline')->nullable();
            $table->text('description')->nullable();
            $table->string('country');
            $table->string('city')->nullable();
            $table->string('industry_category');
            $table->boolean('is_verified')->default(false);
            $table->text('about');

            // FREE fields
            $table->string('website')->nullable();

            // PREMIUM fields (stored in DB, access gated at application layer)
            $table->year('founded_year')->nullable();
            $table->string('company_size')->nullable();
            $table->string('production_capacity')->nullable();
            $table->string('moq')->nullable();
            $table->string('avg_lead_time')->nullable();
            $table->string('carbon_footprint_report')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
