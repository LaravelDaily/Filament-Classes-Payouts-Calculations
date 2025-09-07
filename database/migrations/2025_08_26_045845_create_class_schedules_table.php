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
        Schema::create('class_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_class_id')->constrained('learning_classes');
            $table->date('scheduled_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('substitute_teacher_id')->nullable()->constrained('users');
            $table->integer('student_count')->default(0);
            $table->decimal('teacher_base_pay', 10, 2)->default(0.00);
            $table->decimal('teacher_bonus_pay', 10, 2)->default(0.00);
            $table->decimal('teacher_total_pay', 10, 2)->default(0.00);
            $table->decimal('substitute_base_pay', 10, 2)->default(0.00);
            $table->decimal('substitute_bonus_pay', 10, 2)->default(0.00);
            $table->decimal('substitute_total_pay', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_schedules');
    }
};
