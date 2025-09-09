<?php

use App\Filament\Resources\WeeklySchedules\Pages\CreateWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Pages\EditWeeklySchedule;
use App\Filament\Resources\WeeklySchedules\Pages\ListWeeklySchedules;
use App\Models\ClassType;
use App\Models\Course;
use App\Models\Role;
use App\Models\User;
use App\Models\WeeklySchedule;
use Livewire\Livewire;

beforeEach(function () {
    // Create roles needed for tests
    Role::create(['name' => 'Owner']);
    Role::create(['name' => 'Admin']);
    Role::create(['name' => 'Teacher']);

    // Create class types needed for course factory
    ClassType::create(['name' => 'Regular']);
    ClassType::create(['name' => 'Trial']);
});

test('owner can list weekly schedules', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

    $this->actingAs($owner);

    Livewire::test(ListWeeklySchedules::class)
        ->assertSuccessful();
});

test('admin can list weekly schedules', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(ListWeeklySchedules::class)
        ->assertSuccessful();
});

test('teacher can list weekly schedules', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($teacher);

    Livewire::test(ListWeeklySchedules::class)
        ->assertSuccessful();
});

test('owner can render create weekly schedule page', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

    $this->actingAs($owner);

    Livewire::test(CreateWeeklySchedule::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can render create weekly schedule page', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

    $this->actingAs($admin);

    Livewire::test(CreateWeeklySchedule::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('teacher can render create weekly schedule page', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

    $this->actingAs($teacher);

    Livewire::test(CreateWeeklySchedule::class)
        ->assertSuccessful()
        ->assertFormExists();
});

test('owner can render edit weekly schedule page', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $course = Course::factory()->create(['teacher_id' => $owner->id]);
    $schedule = WeeklySchedule::factory()->create(['course_id' => $course->id]);

    $this->actingAs($owner);

    Livewire::test(EditWeeklySchedule::class, ['record' => $schedule->getRouteKey()])
        ->assertSuccessful()
        ->assertFormExists();
});

test('admin can edit weekly schedule form displays current data', function () {
    $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $course = Course::factory()->create(['teacher_id' => $owner->id]);
    $schedule = WeeklySchedule::factory()->create([
        'course_id' => $course->id,
        'day_of_week' => 1, // Monday
        'start_time' => '10:00',
        'end_time' => '11:00',
    ]);

    $this->actingAs($admin);

    Livewire::test(EditWeeklySchedule::class, ['record' => $schedule->getRouteKey()])
        ->assertSchemaStateSet([
            'course_id' => $course->id,
            'day_of_week' => 1,
            'start_time' => '10:00',
            'end_time' => '11:00',
        ]);
});

test('teacher can edit weekly schedule', function () {
    $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);
    $course = Course::factory()->create(['teacher_id' => $teacher->id]);
    $schedule = WeeklySchedule::factory()->create(['course_id' => $course->id]);

    $this->actingAs($teacher);

    Livewire::test(EditWeeklySchedule::class, ['record' => $schedule->getRouteKey()])
        ->assertSuccessful()
        ->assertFormExists();
});

test('weekly schedule resources have basic CRUD functionality', function () {
    $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
    $course = Course::factory()->create(['teacher_id' => $owner->id]);

    $this->actingAs($owner);

    // Test that we can access the create page
    Livewire::test(CreateWeeklySchedule::class)
        ->assertSuccessful();

    // Test that we can access edit page with existing record
    $schedule = WeeklySchedule::factory()->create(['course_id' => $course->id]);

    Livewire::test(EditWeeklySchedule::class, ['record' => $schedule->getRouteKey()])
        ->assertSuccessful();
});
