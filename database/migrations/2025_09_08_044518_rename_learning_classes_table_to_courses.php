<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::rename('learning_classes', 'courses');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('courses', 'learning_classes');
    }
};
