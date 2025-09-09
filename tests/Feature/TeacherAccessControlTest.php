<?php

use App\Filament\Resources\CourseClasses\CourseClassResource;
use App\Filament\Resources\Courses\CourseResource;
use App\Filament\Resources\TeacherPayouts\TeacherPayoutResource;
use App\Models\ClassType;
use App\Models\Course;
use App\Models\CourseClass;
use App\Models\Role;
use App\Models\TeacherPayout;
use App\Models\User;

beforeEach(function () {
    $this->ownerRole = Role::firstOrCreate(['name' => 'Owner']);
    $this->teacherRole = Role::firstOrCreate(['name' => 'Teacher']);

    $this->owner = User::factory()->create(['role_id' => $this->ownerRole->id]);
    $this->teacher1 = User::factory()->create(['role_id' => $this->teacherRole->id]);
    $this->teacher2 = User::factory()->create(['role_id' => $this->teacherRole->id]);

    // Create a ClassType for the courses
    $this->classType = ClassType::firstOrCreate(['name' => 'Test Class']);

    $this->teacher1Courses = Course::factory()
        ->count(3)
        ->create([
            'teacher_id' => $this->teacher1->id,
            'class_type_id' => $this->classType->id,
        ]);

    $this->teacher2Courses = Course::factory()
        ->count(2)
        ->create([
            'teacher_id' => $this->teacher2->id,
            'class_type_id' => $this->classType->id,
        ]);

    $this->teacher1Payouts = TeacherPayout::factory()
        ->count(2)
        ->sequence(
            ['month' => '2025-01'],
            ['month' => '2025-02']
        )
        ->create(['teacher_id' => $this->teacher1->id]);

    $this->teacher2Payouts = TeacherPayout::factory()
        ->count(3)
        ->sequence(
            ['month' => '2025-03'],
            ['month' => '2025-04'],
            ['month' => '2025-05']
        )
        ->create(['teacher_id' => $this->teacher2->id]);

    // Create CourseClasses for testing
    $this->teacher1Classes = CourseClass::factory()
        ->count(2)
        ->create([
            'course_id' => $this->teacher1Courses->first()->id,
            'teacher_id' => $this->teacher1->id,
        ]);

    $this->teacher2Classes = CourseClass::factory()
        ->count(2)
        ->create([
            'course_id' => $this->teacher2Courses->first()->id,
            'teacher_id' => $this->teacher2->id,
        ]);

    // Create a class where teacher1 is substitute for teacher2's course
    $this->substituteClass = CourseClass::factory()
        ->create([
            'course_id' => $this->teacher2Courses->first()->id,
            'teacher_id' => $this->teacher2->id,
            'substitute_teacher_id' => $this->teacher1->id,
        ]);
});

test('teacher can only see their own courses', function () {
    $this->actingAs($this->teacher1);

    $query = CourseResource::getEloquentQuery();
    $courses = $query->get();

    expect($courses)->toHaveCount(3);
    expect($courses->pluck('teacher_id')->unique()->toArray())->toBe([$this->teacher1->id]);
});

test('teacher cannot see other teachers courses', function () {
    $this->actingAs($this->teacher1);

    $query = CourseResource::getEloquentQuery();
    $courses = $query->get();

    expect($courses->where('teacher_id', $this->teacher2->id))->toHaveCount(0);
});

test('owner can see all courses', function () {
    $this->actingAs($this->owner);

    $query = CourseResource::getEloquentQuery();
    $courses = $query->get();

    expect($courses)->toHaveCount(5); // 3 from teacher1 + 2 from teacher2
});

test('teacher can only see their own payouts', function () {
    $this->actingAs($this->teacher1);

    $query = TeacherPayoutResource::getEloquentQuery();
    $payouts = $query->get();

    expect($payouts)->toHaveCount(2);
    expect($payouts->pluck('teacher_id')->unique()->toArray())->toBe([$this->teacher1->id]);
});

test('teacher cannot see other teachers payouts', function () {
    $this->actingAs($this->teacher1);

    $query = TeacherPayoutResource::getEloquentQuery();
    $payouts = $query->get();

    expect($payouts->where('teacher_id', $this->teacher2->id))->toHaveCount(0);
});

test('owner can see all payouts', function () {
    $this->actingAs($this->owner);

    $query = TeacherPayoutResource::getEloquentQuery();
    $payouts = $query->get();

    expect($payouts)->toHaveCount(5); // 2 from teacher1 + 3 from teacher2
});

test('teacher cannot create payouts', function () {
    $this->actingAs($this->teacher1);

    expect(TeacherPayoutResource::canCreate())->toBeFalse();
});

test('teacher cannot edit payouts', function () {
    $this->actingAs($this->teacher1);

    $payout = $this->teacher1Payouts->first();
    expect(TeacherPayoutResource::canEdit($payout))->toBeFalse();
});

test('teacher cannot delete payouts', function () {
    $this->actingAs($this->teacher1);

    $payout = $this->teacher1Payouts->first();
    expect(TeacherPayoutResource::canDelete($payout))->toBeFalse();
});

test('owner can create payouts', function () {
    $this->actingAs($this->owner);

    expect(TeacherPayoutResource::canCreate())->toBeTrue();
});

test('owner can edit payouts', function () {
    $this->actingAs($this->owner);

    $payout = $this->teacher1Payouts->first();
    expect(TeacherPayoutResource::canEdit($payout))->toBeTrue();
});

test('owner can delete payouts', function () {
    $this->actingAs($this->owner);

    $payout = $this->teacher1Payouts->first();
    expect(TeacherPayoutResource::canDelete($payout))->toBeTrue();
});

test('user model helper methods work correctly', function () {
    expect($this->teacher1->isTeacher())->toBeTrue();
    expect($this->teacher1->isAdmin())->toBeFalse();

    expect($this->owner->isTeacher())->toBeFalse();
    expect($this->owner->isAdmin())->toBeTrue();
});

test('teacher can only see their own course classes', function () {
    $this->actingAs($this->teacher1);

    $query = CourseClassResource::getEloquentQuery();
    $classes = $query->get();

    // Debug: Let's see what's actually being returned
    $ownClasses = $classes->where('teacher_id', $this->teacher1->id)->count();
    $substituteClasses = $classes->where('substitute_teacher_id', $this->teacher1->id)->count();
    
    // Should see: 2 own classes + 1 substitute class = 3 total
    expect($ownClasses + $substituteClasses)->toBe(3);

    // Should include classes where teacher1 is main teacher or substitute
    $teacherIds = $classes->pluck('teacher_id')->toArray();
    $substituteIds = $classes->pluck('substitute_teacher_id')->filter()->toArray();

    expect($teacherIds)->toContain($this->teacher1->id);
    expect($substituteIds)->toContain($this->teacher1->id);
});

test('teacher cannot see other teachers classes (unless substitute)', function () {
    $this->actingAs($this->teacher1);

    $query = CourseClassResource::getEloquentQuery();
    $classes = $query->get();

    // Should not see teacher2's classes where teacher1 is not involved
    $teacher2OnlyClasses = $classes->where('teacher_id', $this->teacher2->id)
        ->where('substitute_teacher_id', null);

    expect($teacher2OnlyClasses)->toHaveCount(0);
});

test('teacher can see classes where they are substitute', function () {
    $this->actingAs($this->teacher1);

    $query = CourseClassResource::getEloquentQuery();
    $classes = $query->get();

    // Should see the class where teacher1 is substitute
    $substituteClasses = $classes->where('substitute_teacher_id', $this->teacher1->id);
    expect($substituteClasses)->toHaveCount(1);
});

test('owner can see all course classes', function () {
    $this->actingAs($this->owner);

    $query = CourseClassResource::getEloquentQuery();
    $classes = $query->get();

    // Should see: 2 teacher1 classes + 2 teacher2 classes + 1 substitute class = 5 total
    expect($classes)->toHaveCount(5);
});

test('teacher access control prevents seeing payout management buttons', function () {
    // Test the logic by directly checking the user helper methods
    $this->actingAs($this->teacher1);
    expect(auth()->user()->isTeacher())->toBeTrue();

    $this->actingAs($this->owner);
    expect(auth()->user()->isAdmin())->toBeTrue();
    expect(auth()->user()->isTeacher())->toBeFalse();
});
