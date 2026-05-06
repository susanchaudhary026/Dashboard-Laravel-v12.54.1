<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add status column to articles if it doesn't exist
        Schema::table('articles', function (Blueprint $table) {
            if (!Schema::hasColumn('articles', 'status')) {
                $table->boolean('status')->default(0);
            }
        });

        // Add status column to categories if it doesn't exist
        Schema::table('categories', function (Blueprint $table) {
            if (!Schema::hasColumn('categories', 'status')) {
                $table->boolean('status')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            if (Schema::hasColumn('articles', 'status')) {
                $table->dropColumn('status');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};