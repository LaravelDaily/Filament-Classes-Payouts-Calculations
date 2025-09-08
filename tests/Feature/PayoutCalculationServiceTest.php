<?php

use App\Models\Attendance;
use App\Models\ClassType;
use App\Models\Course;
use App\Models\CourseClass;
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
    // Clean slate - ensure no existing data for this test month
    \App\Models\CourseClass::whereBetween('scheduled_date', [
        Carbon::parse('2024-01-01'),
        Carbon::parse('2024-01-31')
    ])->delete();
    
    // Arrange
    $teacher = User::factory()->create();
    $course = Course::factory()->create();
    $students = Student::factory(3)->create();

    $courseClass = CourseClass::factory()->create([
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'scheduled_date' => Carbon::parse('2024-01-15'),
    ]);

    // Create attendance for 2 out of 3 students
    foreach ($students->take(2) as $student) {
        Attendance::factory()->create([
            'course_class_id' => $courseClass->id,
            'student_id' => $student->id,
        ]);
    }

    // Act
    $payouts = $this->service->calculatePayoutsForMonth('2024-01');

    // Assert
    expect($payouts)->toHaveCount(1);

    $payout = $payouts->first();
    expect($payout['teacher_id'])->toBe($teacher->id);
    expect($payout['teacher_name'])->toBe($teacher->name);
    expect($payout['month'])->toBe('2024-01');
    expect($payout['total_students'])->toBe(2);
    expect($payout['class_count'])->toBe(1);
    expect($payout['total_pay'])->toBe(55.00); // $50 base + (2 * $2.50 bonus)
});

test('calculates monthly payout summary correctly', function () {
    // Arrange
    $teacher = User::factory()->create();
    $course = Course::factory()->create();

    $courseClass = CourseClass::factory()->create([
        'teacher_id' => $teacher->id,
        'course_id' => $course->id,
        'scheduled_date' => Carbon::parse('2024-01-15'),
    ]);

    // Create students and attendance
    $students = Student::factory(3)->create();
    foreach ($students as $student) {
        Attendance::factory()->create([
            'course_class_id' => $courseClass->id,
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
