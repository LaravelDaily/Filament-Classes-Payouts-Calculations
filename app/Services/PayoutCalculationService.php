<?php

namespace App\Services;

use App\Models\ClassSchedule;
use App\Models\TeacherPayout;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PayoutCalculationService
{
    public function calculatePayoutsForMonth(string $month): Collection
    {
        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $classSchedules = ClassSchedule::whereBetween('scheduled_date', [$monthStart, $monthEnd])
            ->with(['teacher', 'substituteTeacher', 'attendances', 'learningClass'])
            ->get();

        foreach ($classSchedules as $schedule) {
            $this->updateSchedulePayoutCalculations($schedule);
        }

        // Return teacher payouts summary for the month
        return $this->generateTeacherPayoutSummary($month);
    }

    protected function updateSchedulePayoutCalculations(ClassSchedule $schedule): void
    {
        $attendanceCount = $schedule->attendances()->count();
        $basePay = config('teacher_pay.base_pay', 50.00);
        $bonusPerStudent = config('teacher_pay.bonus_per_student', 2.50);

        $bonusPay = $attendanceCount * $bonusPerStudent;
        $totalPay = $basePay + $bonusPay;

        // Update class schedule with payout calculations
        $updateData = [
            'student_count' => $attendanceCount,
            'teacher_base_pay' => $basePay,
            'teacher_bonus_pay' => $bonusPay,
            'teacher_total_pay' => $totalPay,
        ];

        // If there's a substitute teacher, calculate their pay too
        if ($schedule->substitute_teacher_id) {
            $updateData['substitute_base_pay'] = $basePay;
            $updateData['substitute_bonus_pay'] = $bonusPay;
            $updateData['substitute_total_pay'] = $totalPay;
        }

        $schedule->update($updateData);
    }

    protected function generateTeacherPayoutSummary(string $month): Collection
    {
        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        // Get all teachers who worked in this month
        $teacherSchedules = ClassSchedule::whereBetween('scheduled_date', [$monthStart, $monthEnd])
            ->with(['teacher', 'substituteTeacher', 'learningClass'])
            ->get();

        $teacherPayouts = collect();

        // Group by teacher and calculate totals
        $teacherTotals = $teacherSchedules->groupBy('teacher_id');
        foreach ($teacherTotals as $teacherId => $schedules) {
            $teacher = $schedules->first()->teacher;
            $totalPay = $schedules->sum('teacher_total_pay');
            
            $teacherPayouts->push([
                'teacher_id' => $teacherId,
                'teacher_name' => $teacher->name,
                'month' => $month,
                'total_pay' => $totalPay,
                'class_count' => $schedules->count(),
                'total_students' => $schedules->sum('student_count'),
            ]);
        }

        // Group by substitute teacher
        $substituteTotals = $teacherSchedules->where('substitute_teacher_id', '!=', null)
            ->groupBy('substitute_teacher_id');
        
        foreach ($substituteTotals as $teacherId => $schedules) {
            $teacher = $schedules->first()->substituteTeacher;
            $totalPay = $schedules->sum('substitute_total_pay');
            
            // Check if we already have this teacher from regular teaching
            $existingIndex = $teacherPayouts->search(function ($item) use ($teacherId) {
                return $item['teacher_id'] == $teacherId;
            });

            if ($existingIndex !== false) {
                // Add substitute pay to existing teacher record
                $existing = $teacherPayouts[$existingIndex];
                $existing['total_pay'] += $totalPay;
                $existing['substitute_class_count'] = $schedules->count();
                $existing['substitute_students'] = $schedules->sum('student_count');
                $teacherPayouts[$existingIndex] = $existing;
            } else {
                // Create new record for substitute-only teacher
                $teacherPayouts->push([
                    'teacher_id' => $teacherId,
                    'teacher_name' => $teacher->name,
                    'month' => $month,
                    'total_pay' => $totalPay,
                    'class_count' => 0,
                    'total_students' => 0,
                    'substitute_class_count' => $schedules->count(),
                    'substitute_students' => $schedules->sum('student_count'),
                ]);
            }
        }

        return $teacherPayouts;
    }

    public function generatePayoutsForMonth(string $month): array
    {
        $calculatedPayouts = $this->calculatePayoutsForMonth($month);

        $results = [
            'generated' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        DB::transaction(function () use ($calculatedPayouts, $month, &$results) {
            foreach ($calculatedPayouts as $payoutData) {
                try {
                    $existingPayout = TeacherPayout::where([
                        'teacher_id' => $payoutData['teacher_id'],
                        'month' => $month,
                    ])->first();

                    if ($existingPayout) {
                        // Update existing payout only if not paid
                        if (! $existingPayout->is_paid) {
                            $existingPayout->update([
                                'total_pay' => $payoutData['total_pay'],
                            ]);
                            $results['updated']++;
                        } else {
                            $results['skipped']++;
                        }
                    } else {
                        // Create new payout
                        TeacherPayout::create([
                            'teacher_id' => $payoutData['teacher_id'],
                            'month' => $month,
                            'total_pay' => $payoutData['total_pay'],
                        ]);
                        $results['generated']++;
                    }
                } catch (\Exception $e) {
                    $results['errors'][] = "Error processing payout for teacher {$payoutData['teacher_name']}: ".$e->getMessage();
                }
            }
        });

        return $results;
    }

    public function getMonthlyPayoutSummary(string $month): array
    {
        $monthStart = Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        $monthEnd = Carbon::createFromFormat('Y-m', $month)->endOfMonth();

        $existingPayouts = TeacherPayout::where('month', $month)->get();
        $calculatedPayouts = $this->calculatePayoutsForMonth($month);

        return [
            'month' => $month,
            'month_formatted' => $monthStart->format('F Y'),
            'existing_payouts_count' => $existingPayouts->count(),
            'existing_total_amount' => $existingPayouts->sum('total_pay'),
            'calculated_payouts_count' => $calculatedPayouts->count(),
            'calculated_total_amount' => $calculatedPayouts->sum('total_pay'),
            'unique_teachers_count' => $calculatedPayouts->pluck('teacher_id')->unique()->count(),
        ];
    }
}