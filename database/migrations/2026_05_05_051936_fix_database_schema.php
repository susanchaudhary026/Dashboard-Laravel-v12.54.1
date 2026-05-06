<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create categories table if it doesn't exist
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('title')->unique();
                $table->boolean('status')->default(1);
                $table->timestamps();
            });
        }

        // Add missing columns to articles if they don't exist
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'image')) {
                $table->string('image')->nullable();
            }
            if (!Schema::hasColumn('articles', 'status')) {
                $table->boolean('status')->default(1);
            }
            if (!Schema::hasColumn('articles', 'category_id')) {
                $table->unsignedBigInteger('category_id')->nullable();
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            }
        });

        // Add status to categories if it doesn't exist
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'status')) {
                $table->boolean('status')->default(1);
            }
        });
    }

    public function down(): void
    {
        // Rollback logic
    }
};