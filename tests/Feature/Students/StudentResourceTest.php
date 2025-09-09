<?php

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Create roles needed for tests
    Role::create(['name' => 'Owner']);
    Role::create(['name' => 'Admin']);
    Role::create(['name' => 'Teacher']);
});

test('owner can list students', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $students = Student::factory()->count(5)->create();

    $this->actingAs($owner);

    Livewire::test(ListStudents::class)
        ->assertCanSeeTableRecords($students)
        ->assertSuccessful();
});

test('admin can list students', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(ListStudents::class)
        ->assertSuccessful();
});

test('teacher cannot list students', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($teacher);

    Livewire::test(ListStudents::class)
        ->assertForbidden();
});

test('owner can search students by name', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $student1 = Student::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
    $student2 = Student::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith']);

    $this->actingAs($owner);

    Livewire::test(ListStudents::class)
        ->searchTable('John')
        ->assertCanSeeTableRecords([$student1])
        ->assertCanNotSeeTableRecords([$student2]);
});

test('admin can search students by email', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);
    $student1 = Student::factory()->create(['email' => 'john@example.com']);
    $student2 = Student::factory()->create(['email' => 'jane@example.com']);

    $this->actingAs($admin);

    Livewire::test(ListStudents::class)
        ->searchTable('john@example.com')
        ->assertCanSeeTableRecords([$student1])
        ->assertCanNotSeeTableRecords([$student2]);
});

test('owner can render create student page', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

    $this->actingAs($owner);

    Livewire::test(CreateStudent::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can render create student page', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(CreateStudent::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher cannot render create student page', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($teacher);

    Livewire::test(CreateStudent::class)
        ->assertForbidden();
});

test('owner can render edit student page', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $student = Student::factory()->create();

    $this->actingAs($owner);

    Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
        ->assertSuccessful()
        ->assertFormExists()
        ->assertFormFieldExists('first_name')
        ->assertFormFieldExists('last_name')
        ->assertFormFieldExists('email');
});

test('admin can edit student form displays current data', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);
    $student = Student::factory()->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);

    $this->actingAs($admin);

    Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
        ->assertSchemaStateSet([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
        ]);
});

test('teacher cannot edit student', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);
    $student = Student::factory()->create();

    $this->actingAs($teacher);

    Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
        ->assertForbidden();
});
