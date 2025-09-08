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
        Schema::rename('class_schedules', 'course_classes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('course_classes', 'class_schedules');
    }
};
