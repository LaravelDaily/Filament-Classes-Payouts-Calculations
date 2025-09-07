<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\WeeklySchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleGenerationService
{
    public function generateMonthlySchedules(int $year, int $month): array
    {
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        // Check if schedules already exist for this month
        if ($this->hasSchedulesForMonth($year, $month)) {
            throw new \Exception("Schedules for {$startOfMonth->format('F Y')} have already been generated.");
        }
        
        // Get all active weekly schedules
        $weeklySchedules = WeeklySchedule::with(['learningClass', 'teacher', 'substituteTeacher'])
            ->where('is_active', true)
            ->where(function ($query) use ($startOfMonth, $endOfMonth) {
                $query->whereNull('start_date')
                    ->orWhere('start_date', '<=', $endOfMonth);
            })
            ->where(function ($query) use ($startOfMonth) {
                $query->whereNull('end_date')
                    ->orWhere('end_date', '>=', $startOfMonth);
            })
            ->get();
        
        $createdSchedules = [];
        
        foreach ($weeklySchedules as $weeklySchedule) {
            $schedules = $this->generateSchedulesFromWeeklyPattern($weeklySchedule, $startOfMonth, $endOfMonth);
            $createdSchedules = array_merge($createdSchedules, $schedules);
        }
        
        return $createdSchedules;
    }
    
    protected function hasSchedulesForMonth(int $year, int $month): bool
    {
        $startOfMonth = Carbon::create($year, $month, 1);
        $endOfMonth = $startOfMonth->copy()->endOfMonth();
        
        return ClassSchedule::whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
            ->whereNotNull('weekly_schedule_id')
            ->exists();
    }
    
    protected function generateSchedulesFromWeeklyPattern(WeeklySchedule $weeklySchedule, Carbon $startDate, Carbon $endDate): array
    {
        $schedules = [];
        $current = $startDate->copy();
        
        // Find the first occurrence of the target day in the month
        while ($current->dayOfWeekIso !== $weeklySchedule->day_of_week && $current <= $endDate) {
            $current->addDay();
        }
        
        // Generate schedules for each occurrence of this day in the month
        while ($current <= $endDate) {
            // Check if this date is within the weekly schedule's active period
            if ($this->isDateWithinSchedulePeriod($current, $weeklySchedule)) {
                $schedule = $this->createClassScheduleFromWeekly($weeklySchedule, $current->copy());
                $schedules[] = $schedule;
            }
            
            $current->addWeek();
        }
        
        return $schedules;
    }
    
    protected function isDateWithinSchedulePeriod(Carbon $date, WeeklySchedule $weeklySchedule): bool
    {
        if ($weeklySchedule->start_date && $date->lt($weeklySchedule->start_date)) {
            return false;
        }
        
        if ($weeklySchedule->end_date && $date->gt($weeklySchedule->end_date)) {
            return false;
        }
        
        return true;
    }
    
    protected function createClassScheduleFromWeekly(WeeklySchedule $weeklySchedule, Carbon $date): ClassSchedule
    {
        $studentCount = $weeklySchedule->expected_student_count;
        
        $teacherBonusPay = $studentCount * $weeklySchedule->teacher_bonus_per_student;
        $teacherTotalPay = $weeklySchedule->teacher_base_pay + $teacherBonusPay;
        
        $substituteBonusPay = $weeklySchedule->substitute_teacher_id 
            ? $studentCount * $weeklySchedule->substitute_bonus_per_student 
            : 0;
        $substituteTotalPay = $weeklySchedule->substitute_base_pay + $substituteBonusPay;
        
        DB::statement("INSERT INTO class_schedules (learning_class_id, weekly_schedule_id, scheduled_date, start_time, end_time, teacher_id, substitute_teacher_id, student_count, teacher_base_pay, teacher_bonus_pay, teacher_total_pay, substitute_base_pay, substitute_bonus_pay, substitute_total_pay, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())", [
            $weeklySchedule->learning_class_id,
            $weeklySchedule->id,
            $date->format('Y-m-d'),
            $weeklySchedule->start_time,
            $weeklySchedule->end_time,
            $weeklySchedule->teacher_id,
            $weeklySchedule->substitute_teacher_id,
            $studentCount,
            $weeklySchedule->teacher_base_pay,
            $teacherBonusPay,
            $teacherTotalPay,
            $weeklySchedule->substitute_base_pay,
            $substituteBonusPay,
            $substituteTotalPay,
        ]);
        
        return ClassSchedule::where('scheduled_date', $date->format('Y-m-d'))
            ->where('weekly_schedule_id', $weeklySchedule->id)
            ->where('learning_class_id', $weeklySchedule->learning_class_id)
            ->orderBy('id', 'desc')
            ->first();
    }
    
    public function getAvailableMonthsForGeneration(): Collection
    {
        $months = collect();
        $current = now()->startOfMonth();
        
        // Show current month and next 11 months
        for ($i = 0; $i < 12; $i++) {
            $months->push([
                'year' => $current->year,
                'month' => $current->month,
                'label' => $current->format('F Y'),
                'value' => $current->format('Y-m'),
                'has_schedules' => $this->hasSchedulesForMonth($current->year, $current->month),
            ]);
            $current->addMonth();
        }
        
        return $months;
    }
}
