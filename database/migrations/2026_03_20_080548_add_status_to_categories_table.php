<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
        public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('status')->default('unpublished')->change();
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('status', ['published', 'unpublished'])->default('unpublished')->change();
        });
    }
};
