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
        Schema::table('teacher_payouts', function (Blueprint $table) {
            $table->unique(['teacher_id', 'month'], 'unique_teacher_month_payout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_payouts', function (Blueprint $table) {
            $table->dropUnique('unique_teacher_month_payout');
        });
    }
};
