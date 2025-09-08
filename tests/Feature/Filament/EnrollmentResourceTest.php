<?php

use App\Filament\Resources\Enrollments\Pages\CreateEnrollment;
use App\Filament\Resources\Enrollments\Pages\EditEnrollment;
use App\Filament\Resources\Enrollments\Pages\ListEnrollments;
use App\Models\Course;
use App\Models\Enrollment;
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
    \App\Models\ClassType::firstOrCreate(['name' => '1:1 Class']);
    \App\Models\ClassType::firstOrCreate(['name' => 'Group Class']);
});

test('owner can list enrollments', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

    $this->actingAs($owner);

    Livewire::test(ListEnrollments::class)
        ->assertSuccessful();
});

test('admin can list enrollments', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(ListEnrollments::class)
        ->assertSuccessful();
});

test('teacher cannot list enrollments', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($teacher);

    Livewire::test(ListEnrollments::class)
        ->assertForbidden();
});

test('owner can render create enrollment page', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

    // Create required related data
    Student::factory()->create();
    Course::factory()->create();

    $this->actingAs($owner);

    Livewire::test(CreateEnrollment::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can render create enrollment page', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

    // Create required related data
    Student::factory()->create();
    Course::factory()->create();

    $this->actingAs($admin);

    Livewire::test(CreateEnrollment::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('owner can edit enrollment', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $enrollment = Enrollment::factory()->create();

    $this->actingAs($owner);

    Livewire::test(EditEnrollment::class, [
        'record' => $enrollment->id,
    ])
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can edit enrollment', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);
    $enrollment = Enrollment::factory()->create();

    $this->actingAs($admin);

    Livewire::test(EditEnrollment::class, [
        'record' => $enrollment->id,
    ])
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher cannot edit enrollment', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);
    $enrollment = Enrollment::factory()->create();

    $this->actingAs($teacher);

    Livewire::test(EditEnrollment::class, [
        'record' => $enrollment->id,
    ])
        ->assertForbidden();
});
