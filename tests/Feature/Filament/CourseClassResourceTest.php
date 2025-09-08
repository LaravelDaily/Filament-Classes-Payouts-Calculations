<?php

use App\Filament\Resources\CourseClasses\Pages\CreateCourseClass;
use App\Filament\Resources\CourseClasses\Pages\EditCourseClass;
use App\Filament\Resources\CourseClasses\Pages\ListCourseClasses;
use App\Models\ClassType;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Role;
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

test('owner can list course classes', function () {
    $ownerRole = Role::where('name', 'Owner')->first();
    $owner = User::factory()->create(['role_id' => $ownerRole->id]);

    $this->actingAs($owner);

    Livewire::test(ListCourseClasses::class)
        ->assertSuccessful();
});

test('admin can list course classes', function () {
    $admin = createTestUserWithRole('Admin');

    $this->actingAs($admin);

    Livewire::test(ListCourseClasses::class)
        ->assertSuccessful();
});

test('teacher cannot list course classes', function () {
    $teacher = createTestUserWithRole('Teacher');

    $this->actingAs($teacher);

    Livewire::test(ListCourseClasses::class)
        ->assertForbidden();
});

test('owner can render create course class page', function () {
    $owner = createTestUserWithRole('Owner');

    // Create required related data
    Course::factory()->create();
    User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($owner);

    Livewire::test(CreateCourseClass::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can render create course class page', function () {
    $admin = createTestUserWithRole('Admin');

    // Create required related data
    Course::factory()->create();
    User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(CreateCourseClass::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher cannot render create course class page', function () {
    $teacher = createTestUserWithRole('Teacher');

    $this->actingAs($teacher);

    Livewire::test(CreateCourseClass::class)
        ->assertForbidden();
});

test('owner can edit course class', function () {
    $owner = createTestUserWithRole('Owner');
    $courseClass = CourseClass::factory()->create();

    $this->actingAs($owner);

    Livewire::test(EditCourseClass::class, [
        'record' => $courseClass->id,
    ])
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can edit course class', function () {
    $admin = createTestUserWithRole('Admin');
    $courseClass = CourseClass::factory()->create();

    $this->actingAs($admin);

    Livewire::test(EditCourseClass::class, [
        'record' => $courseClass->id,
    ])
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher cannot edit course class', function () {
    $teacher = createTestUserWithRole('Teacher');
    $courseClass = CourseClass::factory()->create();

    $this->actingAs($teacher);

    Livewire::test(EditCourseClass::class, [
        'record' => $courseClass->id,
    ])
        ->assertForbidden();
});

test('course classes can be searched in table', function () {
    $owner = createTestUserWithRole('Owner');
    $course = Course::factory()->create(['name' => 'Searchable Math Course']);
    $courseClass = CourseClass::factory()->create([
        'course_id' => $course->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ListCourseClasses::class)
        ->assertCanSeeTableRecords([$courseClass])
        ->searchTable('Searchable Math Course')
        ->assertCanSeeTableRecords([$courseClass]);
});

test('course classes can be filtered by course', function () {
    $owner = createTestUserWithRole('Owner');
    $course1 = Course::factory()->create(['name' => 'Math Course']);
    $course2 = Course::factory()->create(['name' => 'Science Course']);

    $courseClass1 = CourseClass::factory()->create(['course_id' => $course1->id]);
    $courseClass2 = CourseClass::factory()->create(['course_id' => $course2->id]);

    $this->actingAs($owner);

    Livewire::test(ListCourseClasses::class)
        ->assertCanSeeTableRecords([$courseClass1, $courseClass2])
        ->filterTable('course_id', $course1->id)
        ->assertCanSeeTableRecords([$courseClass1])
        ->assertCanNotSeeTableRecords([$courseClass2]);
});

test('course classes can be filtered by teacher', function () {
    $owner = createTestUserWithRole('Owner');
    $teacher1 = createTestUserWithRole('Teacher');
    $teacher2 = createTestUserWithRole('Teacher');

    $courseClass1 = CourseClass::factory()->create(['teacher_id' => $teacher1->id]);
    $courseClass2 = CourseClass::factory()->create(['teacher_id' => $teacher2->id]);

    $this->actingAs($owner);

    Livewire::test(ListCourseClasses::class)
        ->assertCanSeeTableRecords([$courseClass1, $courseClass2])
        ->filterTable('teacher_id', $teacher1->id)
        ->assertCanSeeTableRecords([$courseClass1])
        ->assertCanNotSeeTableRecords([$courseClass2]);
});
