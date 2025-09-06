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

        $payoutData = collect();

        foreach ($classSchedules as $schedule) {
            // Calculate for main teacher
            if ($schedule->teacher_id) {
                $payoutData->push($this->calculatePayoutForSchedule($schedule, $schedule->teacher, false, $month));
            }

            // Calculate for substitute teacher if present
            if ($schedule->substitute_teacher_id && $schedule->substitute_teacher_id !== $schedule->teacher_id) {
                $payoutData->push($this->calculatePayoutForSchedule($schedule, $schedule->substituteTeacher, true, $month));
            }
        }

        return $payoutData->filter(); // Remove null values
    }

    protected function calculatePayoutForSchedule(ClassSchedule $schedule, User $teacher, bool $isSubstitute, string $month): ?array
    {
        if (! $teacher) {
            return null;
        }

        $attendanceCount = $schedule->attendances()->count();
        $basePay = config('teacher_pay.base_pay', 50.00);
        $bonusPerStudent = config('teacher_pay.bonus_per_student', 2.50);

        $bonusPay = $attendanceCount * $bonusPerStudent;
        $totalPay = $basePay + $bonusPay;

        return [
            'class_schedule_id' => $schedule->id,
            'teacher_id' => $teacher->id,
            'teacher_name' => $teacher->name,
            'class_name' => $schedule->learningClass->name ?? 'N/A',
            'class_date' => $schedule->scheduled_date->format('Y-m-d'),
            'month' => $month,
            'student_count' => $attendanceCount,
            'is_substitute' => $isSubstitute,
            'base_pay' => $basePay,
            'bonus_pay' => $bonusPay,
            'total_pay' => $totalPay,
        ];
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

        DB::transaction(function () use ($calculatedPayouts, &$results) {
            foreach ($calculatedPayouts as $payoutData) {
                try {
                    $existingPayout = TeacherPayout::where([
                        'class_schedule_id' => $payoutData['class_schedule_id'],
                        'teacher_id' => $payoutData['teacher_id'],
                        'month' => $payoutData['month'],
                    ])->first();

                    if ($existingPayout) {
                        // Update existing payout only if not paid
                        if (! $existingPayout->is_paid) {
                            $existingPayout->update([
                                'student_count' => $payoutData['student_count'],
                                'is_substitute' => $payoutData['is_substitute'],
                                'base_pay' => $payoutData['base_pay'],
                                'bonus_pay' => $payoutData['bonus_pay'],
                                'total_pay' => $payoutData['total_pay'],
                            ]);
                            $results['updated']++;
                        } else {
                            $results['skipped']++;
                        }
                    } else {
                        // Create new payout
                        TeacherPayout::create([
                            'class_schedule_id' => $payoutData['class_schedule_id'],
                            'teacher_id' => $payoutData['teacher_id'],
                            'month' => $payoutData['month'],
                            'student_count' => $payoutData['student_count'],
                            'is_substitute' => $payoutData['is_substitute'],
                            'base_pay' => $payoutData['base_pay'],
                            'bonus_pay' => $payoutData['bonus_pay'],
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
