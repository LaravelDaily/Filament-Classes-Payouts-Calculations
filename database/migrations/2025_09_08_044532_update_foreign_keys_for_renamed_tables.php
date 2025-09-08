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
        // Rename learning_class_id to course_id in course_classes table
        Schema::table('course_classes', function (Blueprint $table) {
            $table->renameColumn('learning_class_id', 'course_id');
        });

        // Rename class_schedule_id to course_class_id in attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('class_schedule_id', 'course_class_id');
        });

        // Rename learning_class_id to course_id in enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->renameColumn('learning_class_id', 'course_id');
        });

        // Rename learning_class_id to course_id in weekly_schedules table
        Schema::table('weekly_schedules', function (Blueprint $table) {
            $table->renameColumn('learning_class_id', 'course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert course_id back to learning_class_id in weekly_schedules table
        Schema::table('weekly_schedules', function (Blueprint $table) {
            $table->renameColumn('course_id', 'learning_class_id');
        });

        // Revert course_id back to learning_class_id in enrollments table
        Schema::table('enrollments', function (Blueprint $table) {
            $table->renameColumn('course_id', 'learning_class_id');
        });

        // Revert course_class_id back to class_schedule_id in attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->renameColumn('course_class_id', 'class_schedule_id');
        });

        // Revert course_id back to learning_class_id in course_classes table
        Schema::table('course_classes', function (Blueprint $table) {
            $table->renameColumn('course_id', 'learning_class_id');
        });
    }
};
