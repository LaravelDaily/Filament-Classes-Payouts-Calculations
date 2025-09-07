<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear all existing data since we're restructuring
        DB::table('teacher_payouts')->truncate();
        
        Schema::table('teacher_payouts', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['class_schedule_id']);
            
            // Drop the old unique constraint
            $table->dropUnique('unique_teacher_payout');
            
            // Drop columns
            $table->dropColumn([
                'class_schedule_id',
                'student_count',
                'is_substitute',
                'base_pay',
                'bonus_pay',
            ]);
            
            // Add new unique constraint on existing columns
            $table->unique(['teacher_id', 'month'], 'unique_teacher_month_payout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_payouts', function (Blueprint $table) {
            // Drop new constraint
            $table->dropUnique('unique_teacher_month_payout');
            
            // Add back old columns
            $table->foreignId('class_schedule_id')->constrained('class_schedules');
            $table->integer('student_count')->default(0);
            $table->boolean('is_substitute')->default(false);
            $table->decimal('base_pay', 10, 2)->default(0.00);
            $table->decimal('bonus_pay', 10, 2)->default(0.00);
            
            // Add back old unique constraint
            $table->unique(['class_schedule_id', 'teacher_id', 'month'], 'unique_teacher_payout');
        });
    }
};
