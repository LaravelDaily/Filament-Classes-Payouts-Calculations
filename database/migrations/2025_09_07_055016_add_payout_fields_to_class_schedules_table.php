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
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->integer('student_count')->default(0)->after('substitute_teacher_id');
            $table->decimal('teacher_base_pay', 10, 2)->default(0.00)->after('student_count');
            $table->decimal('teacher_bonus_pay', 10, 2)->default(0.00)->after('teacher_base_pay');
            $table->decimal('teacher_total_pay', 10, 2)->default(0.00)->after('teacher_bonus_pay');
            $table->decimal('substitute_base_pay', 10, 2)->default(0.00)->after('teacher_total_pay');
            $table->decimal('substitute_bonus_pay', 10, 2)->default(0.00)->after('substitute_base_pay');
            $table->decimal('substitute_total_pay', 10, 2)->default(0.00)->after('substitute_bonus_pay');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_schedules', function (Blueprint $table) {
            $table->dropColumn([
                'student_count',
                'teacher_base_pay',
                'teacher_bonus_pay',
                'teacher_total_pay',
                'substitute_base_pay',
                'substitute_bonus_pay',
                'substitute_total_pay',
            ]);
        });
    }
};
