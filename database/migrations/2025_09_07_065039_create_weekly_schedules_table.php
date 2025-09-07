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
        Schema::create('weekly_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_class_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('users')->onDelete('set null');
            $table->tinyInteger('day_of_week'); // 1=Monday, 2=Tuesday, ..., 7=Sunday
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('expected_student_count')->default(0);
            $table->decimal('teacher_base_pay', 8, 2)->default(0);
            $table->decimal('teacher_bonus_per_student', 8, 2)->default(0);
            $table->decimal('substitute_base_pay', 8, 2)->default(0);
            $table->decimal('substitute_bonus_per_student', 8, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->date('start_date')->nullable(); // When this weekly schedule starts being effective
            $table->date('end_date')->nullable(); // When this weekly schedule ends
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('weekly_schedules');
    }
};
