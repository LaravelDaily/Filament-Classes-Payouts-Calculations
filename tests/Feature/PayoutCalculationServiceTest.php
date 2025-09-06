<?php

use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\ClassType;
use App\Models\LearningClass;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use App\Services\PayoutCalculationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new PayoutCalculationService;

    // Create required data for tests
    Role::create(['name' => 'Teacher']);
    ClassType::create(['name' => 'Regular Class']);
});

test('calculates payouts for month correctly', function () {
    // Arrange
    $teacher = User::factory()->create();
    $learningClass = LearningClass::factory()->create();
    $students = Student::factory(3)->create();

    $schedule = ClassSchedule::factory()->create([
        'teacher_id' => $teacher->id,
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => Carbon::parse('2024-01-15'),
    ]);

    // Create attendance for 2 out of 3 students
    foreach ($students->take(2) as $student) {
        Attendance::factory()->create([
            'class_schedule_id' => $schedule->id,
            'student_id' => $student->id,
        ]);
    }

    // Act
    $payouts = $this->service->calculatePayoutsForMonth('2024-01');

    // Assert
    expect($payouts)->toHaveCount(1);

    $payout = $payouts->first();
    expect($payout['teacher_id'])->toBe($teacher->id);
    expect($payout['class_schedule_id'])->toBe($schedule->id);
    expect($payout['month'])->toBe('2024-01');
    expect($payout['student_count'])->toBe(2);
    expect($payout['is_substitute'])->toBeFalse();
    expect($payout['base_pay'])->toBe(50.00);
    expect($payout['bonus_pay'])->toBe(5.00); // 2 students * $2.50
    expect($payout['total_pay'])->toBe(55.00);
});

test('calculates monthly payout summary correctly', function () {
    // Arrange
    $teacher = User::factory()->create();
    $learningClass = LearningClass::factory()->create();

    $schedule = ClassSchedule::factory()->create([
        'teacher_id' => $teacher->id,
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => Carbon::parse('2024-01-15'),
    ]);

    // Create students and attendance
    $students = Student::factory(3)->create();
    foreach ($students as $student) {
        Attendance::factory()->create([
            'class_schedule_id' => $schedule->id,
            'student_id' => $student->id,
        ]);
    }

    // Act
    $summary = $this->service->getMonthlyPayoutSummary('2024-01');

    // Assert
    expect($summary['month'])->toBe('2024-01');
    expect($summary['month_formatted'])->toBe('January 2024');
    expect($summary['calculated_payouts_count'])->toBe(1);
    expect($summary['calculated_total_amount'])->toBe(57.50); // $50 base + (3 * $2.50 bonus)
    expect($summary['unique_teachers_count'])->toBe(1);
    expect($summary['existing_payouts_count'])->toBe(0);
});
