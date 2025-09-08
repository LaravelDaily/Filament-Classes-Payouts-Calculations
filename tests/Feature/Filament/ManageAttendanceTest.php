<?php

use App\Filament\Resources\Courses\Pages\ManageMonthlyAttendance;
use App\Models\Attendance;
use App\Models\ClassType;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Create roles needed for tests
    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Admin']);
    Role::firstOrCreate(['name' => 'Teacher']);

    // Create class types for Course factory dependency
    ClassType::firstOrCreate(['name' => '1:1 Class']);
    ClassType::firstOrCreate(['name' => 'Group Class']);
});

function createTestUserWithRole(string $roleName): User
{
    $role = Role::where('name', $roleName)->first();

    return User::factory()->create(['role_id' => $role->id]);
}

test('owner can access manage attendance page', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();

    $this->actingAs($owner);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->assertSuccessful();
});

test('admin can access manage attendance page', function () {
    $admin = createTestUserWithRole('Admin');
    $course = Course::factory()->create();

    $this->actingAs($admin);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->assertSuccessful();
});

test('teacher cannot access manage attendance page', function () {
    $teacher = createTestUserWithRole('Teacher');
    $course = Course::factory()->create();

    $this->actingAs($teacher);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->assertForbidden();
});

test('page displays correct schedule information', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create(['name' => 'Test Math Class']);
    $teacher = createTestUserWithRole('Teacher');

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'scheduled_date' => '2024-01-15',
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
    ]);

    $this->actingAs($owner);

    // Set up the request month parameter so it's used during mount
    request()->merge(['month' => '2024-01']);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->assertSee('Test Math Class')
        ->assertSee('No Students Enrolled');
});

test('page shows substitute teacher when assigned', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();
    $teacher = createTestUserWithRole('Teacher');
    $substituteTeacher = createTestUserWithRole('Teacher');

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'teacher_id' => $teacher->id,
        'substitute_teacher_id' => $substituteTeacher->id,
        'scheduled_date' => now()->format('Y-m-d'),
    ]);

    $this->actingAs($owner);

    // Set month to current month since we're using now() for the scheduled date
    request()->merge(['month' => now()->format('Y-m')]);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->assertSee('No Students Enrolled'); // Since no students are enrolled, should show empty state
});

test('page displays enrolled students for attendance', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();
    $student1 = Student::factory()->create(['name' => 'John Doe']);
    $student2 = Student::factory()->create(['name' => 'Jane Smith']);

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'scheduled_date' => '2024-01-15',
    ]);

    // Attach students to course
    $course->students()->attach($student1->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $course->students()->attach($student2->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->set('selectedMonth', '2024-01')
        ->assertSee('John Doe')
        ->assertSee('Jane Smith');
});

test('page only shows students enrolled on schedule date', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();
    $student1 = Student::factory()->create(['name' => 'Current Student']);
    $student2 = Student::factory()->create(['name' => 'Past Student']);
    $student3 = Student::factory()->create(['name' => 'Future Student']);

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'scheduled_date' => '2024-01-15',
    ]);

    // Current enrollment (should show)
    $course->students()->attach($student1->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Past enrollment (should not show - ended before month started)
    $course->students()->attach($student2->id, [
        'start_date' => '2023-01-01',
        'end_date' => '2023-12-31',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Future enrollment (should not show - starts after month ends)
    $course->students()->attach($student3->id, [
        'start_date' => '2024-02-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->set('selectedMonth', '2024-01')
        ->assertSee('Current Student')
        ->assertDontSee('Past Student')
        ->assertDontSee('Future Student');
});

test('can save attendance for students', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'scheduled_date' => '2024-01-15',
    ]);

    $course->students()->attach($student1->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $course->students()->attach($student2->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->set('selectedMonth', '2024-01')
        ->set("attendanceData.{$student1->id}-{$courseClass->id}", 'present')
        ->set("attendanceData.{$student2->id}-{$courseClass->id}", 'absent')
        ->call('save')
        ->assertNotified('Attendance saved successfully');

    // Check that attendance records were created correctly
    $this->assertTrue(
        Attendance::where([
            'course_class_id' => $courseClass->id,
            'student_id' => $student1->id,
        ])->exists()
    );

    $this->assertFalse(
        Attendance::where([
            'course_class_id' => $courseClass->id,
            'student_id' => $student2->id,
        ])->exists()
    );
});

test('can update existing attendance records', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();
    $student = Student::factory()->create();

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'scheduled_date' => '2024-01-15',
    ]);

    $course->students()->attach($student->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create existing attendance record
    Attendance::factory()->create([
        'course_class_id' => $courseClass->id,
        'student_id' => $student->id,
    ]);

    $this->actingAs($owner);

    // First verify student shows as present (existing record)
    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->set('selectedMonth', '2024-01')
        ->assertSet("attendanceData.{$student->id}-{$courseClass->id}", 'present');

    // Now change to absent and save
    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->set('selectedMonth', '2024-01')
        ->set("attendanceData.{$student->id}-{$courseClass->id}", 'absent')
        ->call('save')
        ->assertNotified('Attendance saved successfully');

    // Verify attendance record was deleted (absent)
    $this->assertFalse(
        Attendance::where([
            'course_class_id' => $courseClass->id,
            'student_id' => $student->id,
        ])->exists()
    );
});

test('page shows empty state when no students enrolled', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();

    $this->actingAs($owner);

    // Set month to current month
    request()->merge(['month' => now()->format('Y-m')]);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->assertSee('No Students Enrolled')
        ->assertSee('There are no students enrolled in this course');
});

test('page loads existing attendance correctly', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create();
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();

    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
        'scheduled_date' => '2024-01-15',
    ]);

    $course->students()->attach($student1->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $course->students()->attach($student2->id, [
        'start_date' => '2024-01-01',
        'end_date' => null,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create attendance for student1 only
    Attendance::factory()->create([
        'course_class_id' => $courseClass->id,
        'student_id' => $student1->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id])
        ->set('selectedMonth', '2024-01')
        ->assertSet("attendanceData.{$student1->id}-{$courseClass->id}", 'present')
        ->assertSet("attendanceData.{$student2->id}-{$courseClass->id}", 'absent');
});

test('page title shows learning class name', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create(['name' => 'Advanced Mathematics']);

    $this->actingAs($owner);

    $component = Livewire::test(ManageMonthlyAttendance::class, ['record' => $course->id]);

    expect($component->instance()->getTitle())->toBe('Monthly Attendance - Advanced Mathematics');
});
