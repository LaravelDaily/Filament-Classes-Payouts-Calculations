<?php

use App\Filament\Pages\ManageAttendance;
use App\Models\Attendance;
use App\Models\ClassSchedule;
use App\Models\ClassType;
use App\Models\Enrollment;
use App\Models\LearningClass;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Create roles needed for tests
    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Admin']);
    Role::firstOrCreate(['name' => 'Teacher']);

    // Create class types for LearningClass factory dependency
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
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSuccessful();
});

test('admin can access manage attendance page', function () {
    $admin = createTestUserWithRole('Admin');
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($admin);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSuccessful();
});

test('teacher cannot access manage attendance page', function () {
    $teacher = createTestUserWithRole('Teacher');
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($teacher);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertForbidden();
});

test('page displays correct schedule information', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create(['name' => 'Test Math Class']);
    $teacher = createTestUserWithRole('Teacher');

    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
        'teacher_id' => $teacher->id,
        'scheduled_date' => '2024-01-15',
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSee('Test Math Class')
        ->assertSee('Jan 15, 2024')
        ->assertSee('10:00:00 - 11:00:00')
        ->assertSee($teacher->name);
});

test('page shows substitute teacher when assigned', function () {
    $owner = createTestUserWithRole('Owner');
    $teacher = createTestUserWithRole('Teacher');
    $substituteTeacher = createTestUserWithRole('Teacher');

    $classSchedule = ClassSchedule::factory()->create([
        'teacher_id' => $teacher->id,
        'substitute_teacher_id' => $substituteTeacher->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSee($substituteTeacher->name)
        ->assertDontSee($teacher->name);
});

test('page displays enrolled students for attendance', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create();
    $student1 = Student::factory()->create(['name' => 'John Doe']);
    $student2 = Student::factory()->create(['name' => 'Jane Smith']);

    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => '2024-01-15',
    ]);

    // Create enrollments
    Enrollment::factory()->create([
        'student_id' => $student1->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    Enrollment::factory()->create([
        'student_id' => $student2->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSee('John Doe')
        ->assertSee('Jane Smith');
});

test('page only shows students enrolled on schedule date', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create();
    $student1 = Student::factory()->create(['name' => 'Current Student']);
    $student2 = Student::factory()->create(['name' => 'Past Student']);
    $student3 = Student::factory()->create(['name' => 'Future Student']);

    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => '2024-01-15',
    ]);

    // Current enrollment (should show)
    Enrollment::factory()->create([
        'student_id' => $student1->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    // Past enrollment (should not show)
    Enrollment::factory()->create([
        'student_id' => $student2->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2023-01-01',
        'end_date' => '2024-01-10',
    ]);

    // Future enrollment (should not show)
    Enrollment::factory()->create([
        'student_id' => $student3->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-20',
        'end_date' => null,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSee('Current Student')
        ->assertDontSee('Past Student')
        ->assertDontSee('Future Student');
});

test('can save attendance for students', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create();
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();

    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => '2024-01-15',
    ]);

    Enrollment::factory()->create([
        'student_id' => $student1->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    Enrollment::factory()->create([
        'student_id' => $student2->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->set("attendanceData.{$student1->id}", 'present')
        ->set("attendanceData.{$student2->id}", 'absent')
        ->call('save')
        ->assertNotified('Attendance saved successfully');

    // Check that attendance records were created correctly
    $this->assertTrue(
        Attendance::where([
            'class_schedule_id' => $classSchedule->id,
            'student_id' => $student1->id,
        ])->exists()
    );

    $this->assertFalse(
        Attendance::where([
            'class_schedule_id' => $classSchedule->id,
            'student_id' => $student2->id,
        ])->exists()
    );
});

test('can update existing attendance records', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create();
    $student = Student::factory()->create();

    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => '2024-01-15',
    ]);

    Enrollment::factory()->create([
        'student_id' => $student->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    // Create existing attendance record
    Attendance::factory()->create([
        'class_schedule_id' => $classSchedule->id,
        'student_id' => $student->id,
    ]);

    $this->actingAs($owner);

    // First verify student shows as present (existing record)
    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSet("attendanceData.{$student->id}", 'present');

    // Now change to absent and save
    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->set("attendanceData.{$student->id}", 'absent')
        ->call('save')
        ->assertNotified('Attendance saved successfully');

    // Verify attendance record was deleted (absent)
    $this->assertFalse(
        Attendance::where([
            'class_schedule_id' => $classSchedule->id,
            'student_id' => $student->id,
        ])->exists()
    );
});

test('page shows empty state when no students enrolled', function () {
    $owner = createTestUserWithRole('Owner');
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSee('No Enrolled Students')
        ->assertSee('There are no students enrolled in this class');
});

test('page loads existing attendance correctly', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create();
    $student1 = Student::factory()->create();
    $student2 = Student::factory()->create();

    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
        'scheduled_date' => '2024-01-15',
    ]);

    Enrollment::factory()->create([
        'student_id' => $student1->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    Enrollment::factory()->create([
        'student_id' => $student2->id,
        'learning_class_id' => $learningClass->id,
        'start_date' => '2024-01-01',
        'end_date' => null,
    ]);

    // Create attendance for student1 only
    Attendance::factory()->create([
        'class_schedule_id' => $classSchedule->id,
        'student_id' => $student1->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id])
        ->assertSet("attendanceData.{$student1->id}", 'present')
        ->assertSet("attendanceData.{$student2->id}", 'absent');
});

test('page title shows learning class name', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create(['name' => 'Advanced Mathematics']);
    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
    ]);

    $this->actingAs($owner);

    $component = Livewire::test(ManageAttendance::class, ['record' => $classSchedule->id]);

    expect($component->instance()->getTitle())->toBe('Manage Attendance - Advanced Mathematics');
});
