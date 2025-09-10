<?php

namespace App\Services;

use App\Models\CourseClass;
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
        $weeklySchedules = WeeklySchedule::with(['course', 'teacher', 'substituteTeacher'])
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

        return CourseClass::whereBetween('scheduled_date', [$startOfMonth, $endOfMonth])
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
                $schedule = $this->createCourseClassFromWeekly($weeklySchedule, $current->copy());
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

    protected function createCourseClassFromWeekly(WeeklySchedule $weeklySchedule, Carbon $date): CourseClass
    {
        return CourseClass::create([
            'course_id' => $weeklySchedule->course_id,
            'weekly_schedule_id' => $weeklySchedule->id,
            'scheduled_date' => $date->format('Y-m-d'),
            'start_time' => $weeklySchedule->start_time,
            'end_time' => $weeklySchedule->end_time,
            'teacher_id' => $weeklySchedule->course->teacher_id,
        ]);
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
