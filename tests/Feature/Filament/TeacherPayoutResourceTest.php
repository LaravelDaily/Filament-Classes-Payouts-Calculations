<?php

use App\Filament\Resources\TeacherPayouts\Pages\ListTeacherPayouts;
use App\Models\Role;
use App\Models\TeacherPayout;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // Ensure roles exist for auth in tests
    Role::firstOrCreate(['name' => 'Owner']);
    Role::firstOrCreate(['name' => 'Admin']);
    Role::firstOrCreate(['name' => 'Teacher']);
});

function createOwnerUser(): User {
    return User::factory()->create([
        'role_id' => Role::where('name', 'Owner')->first()->id,
    ]);
}

test('bulk action Mark as Paid updates selected payouts', function () {
    $owner = createOwnerUser();
    $teacher1 = User::factory()->create([
        'role_id' => Role::where('name', 'Teacher')->first()->id,
    ]);
    $teacher2 = User::factory()->create([
        'role_id' => Role::where('name', 'Teacher')->first()->id,
    ]);

    // Create two unpaid payouts and one already paid
    $unpaid1 = TeacherPayout::factory()->create([
        'teacher_id' => $teacher1->id,
        'month' => now()->format('Y-m'),
        'is_paid' => false,
        'paid_at' => null,
    ]);

    $unpaid2 = TeacherPayout::factory()->create([
        'teacher_id' => $teacher2->id,
        'month' => now()->format('Y-m'),
        'is_paid' => false,
        'paid_at' => null,
    ]);

    $paid = TeacherPayout::factory()->create([
        'teacher_id' => $teacher1->id,
        'month' => now()->copy()->subMonth()->format('Y-m'),
        'is_paid' => true,
        'paid_at' => now()->subDay(),
    ]);

    $this->actingAs($owner);

    // Ensure the bulk action exists and then call it on the unpaid records
    Livewire::test(ListTeacherPayouts::class)
        ->assertTableBulkActionExists('markAsPaid')
        ->callTableBulkAction('markAsPaid', [$unpaid1, $unpaid2]);

    // Refresh models and assert updates
    $unpaid1->refresh();
    $unpaid2->refresh();
    $paid->refresh();

    expect($unpaid1->is_paid)->toBeTrue();
    expect($unpaid1->paid_at)->not->toBeNull();

    expect($unpaid2->is_paid)->toBeTrue();
    expect($unpaid2->paid_at)->not->toBeNull();

    // Already paid record remains paid and retains a paid_at timestamp
    expect($paid->is_paid)->toBeTrue();
    expect($paid->paid_at)->not->toBeNull();
});
