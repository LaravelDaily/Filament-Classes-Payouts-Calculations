<?php

use App\Filament\Resources\ClassSchedules\Pages\CreateClassSchedule;
use App\Filament\Resources\ClassSchedules\Pages\EditClassSchedule;
use App\Filament\Resources\ClassSchedules\Pages\ListClassSchedules;
use App\Models\ClassSchedule;
use App\Models\ClassType;
use App\Models\LearningClass;
use App\Models\Role;
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

test('owner can list class schedules', function () {
    $owner = createTestUserWithRole('Owner');

    $this->actingAs($owner);

    Livewire::test(ListClassSchedules::class)
        ->assertSuccessful();
});

test('admin can list class schedules', function () {
    $admin = createTestUserWithRole('Admin');

    $this->actingAs($admin);

    Livewire::test(ListClassSchedules::class)
        ->assertSuccessful();
});

test('teacher cannot list class schedules', function () {
    $teacher = createTestUserWithRole('Teacher');

    $this->actingAs($teacher);

    Livewire::test(ListClassSchedules::class)
        ->assertForbidden();
});

test('owner can render create class schedule page', function () {
    $owner = createTestUserWithRole('Owner');

    // Create required related data
    LearningClass::factory()->create();
    User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($owner);

    Livewire::test(CreateClassSchedule::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can render create class schedule page', function () {
    $admin = createTestUserWithRole('Admin');

    // Create required related data
    LearningClass::factory()->create();
    User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(CreateClassSchedule::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher cannot render create class schedule page', function () {
    $teacher = createTestUserWithRole('Teacher');

    $this->actingAs($teacher);

    Livewire::test(CreateClassSchedule::class)
        ->assertForbidden();
});

test('owner can edit class schedule', function () {
    $owner = createTestUserWithRole('Owner');
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($owner);

    Livewire::test(EditClassSchedule::class, [
        'record' => $classSchedule->id,
    ])
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can edit class schedule', function () {
    $admin = createTestUserWithRole('Admin');
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($admin);

    Livewire::test(EditClassSchedule::class, [
        'record' => $classSchedule->id,
    ])
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher cannot edit class schedule', function () {
    $teacher = createTestUserWithRole('Teacher');
    $classSchedule = ClassSchedule::factory()->create();

    $this->actingAs($teacher);

    Livewire::test(EditClassSchedule::class, [
        'record' => $classSchedule->id,
    ])
        ->assertForbidden();
});

test('class schedules can be searched in table', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass = LearningClass::factory()->create(['name' => 'Searchable Math Class']);
    $classSchedule = ClassSchedule::factory()->create([
        'learning_class_id' => $learningClass->id,
    ]);

    $this->actingAs($owner);

    Livewire::test(ListClassSchedules::class)
        ->assertCanSeeTableRecords([$classSchedule])
        ->searchTable('Searchable Math Class')
        ->assertCanSeeTableRecords([$classSchedule]);
});

test('class schedules can be filtered by learning class', function () {
    $owner = createTestUserWithRole('Owner');
    $learningClass1 = LearningClass::factory()->create(['name' => 'Math Class']);
    $learningClass2 = LearningClass::factory()->create(['name' => 'Science Class']);
    
    $schedule1 = ClassSchedule::factory()->create(['learning_class_id' => $learningClass1->id]);
    $schedule2 = ClassSchedule::factory()->create(['learning_class_id' => $learningClass2->id]);

    $this->actingAs($owner);

    Livewire::test(ListClassSchedules::class)
        ->assertCanSeeTableRecords([$schedule1, $schedule2])
        ->filterTable('learning_class_id', $learningClass1->id)
        ->assertCanSeeTableRecords([$schedule1])
        ->assertCanNotSeeTableRecords([$schedule2]);
});

test('class schedules can be filtered by teacher', function () {
    $owner = createTestUserWithRole('Owner');
    $teacher1 = createTestUserWithRole('Teacher');
    $teacher2 = createTestUserWithRole('Teacher');
    
    $schedule1 = ClassSchedule::factory()->create(['teacher_id' => $teacher1->id]);
    $schedule2 = ClassSchedule::factory()->create(['teacher_id' => $teacher2->id]);

    $this->actingAs($owner);

    Livewire::test(ListClassSchedules::class)
        ->assertCanSeeTableRecords([$schedule1, $schedule2])
        ->filterTable('teacher_id', $teacher1->id)
        ->assertCanSeeTableRecords([$schedule1])
        ->assertCanNotSeeTableRecords([$schedule2]);
});