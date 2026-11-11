<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->index('industry_category');
            $table->index('country');
            $table->index(['is_verified', 'name']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->index('supplier_id');
        });

        Schema::table('supplier_tags', function (Blueprint $table) {
            $table->index('supplier_id');
        });

        Schema::table('supplier_certifications', function (Blueprint $table) {
            $table->index('supplier_id');
            $table->index('certification_id');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropIndex(['industry_category']);
            $table->dropIndex(['country']);
            $table->dropIndex(['is_verified', 'name']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
        });

        Schema::table('supplier_tags', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
        });

        Schema::table('supplier_certifications', function (Blueprint $table) {
            $table->dropIndex(['supplier_id']);
            $table->dropIndex(['certification_id']);
        });
    }
};
