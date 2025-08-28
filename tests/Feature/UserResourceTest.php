<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_owner_can_list_users(): void
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

        $this->actingAs($owner);

        Livewire::test(ListUsers::class)
            ->assertSuccessful();
    }

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

        $this->actingAs($admin);

        Livewire::test(ListUsers::class)
            ->assertSuccessful();
    }

    public function test_teacher_cannot_list_users(): void
    {
        $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

        $this->actingAs($teacher);

        Livewire::test(ListUsers::class)
            ->assertForbidden();
    }

    public function test_owner_can_create_user(): void
    {
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $teacherRole = Role::where('name', 'Teacher')->first();

        $this->actingAs($owner);

        Livewire::test(CreateUser::class)
            ->fillForm([
                'name' => 'New Teacher',
                'email' => 'newteacher@example.com',
                'role_id' => $teacherRole->id,
                'password' => 'password123',
            ])
            ->call('create')
            ->assertNotified();

        $this->assertDatabaseHas(User::class, [
            'name' => 'New Teacher',
            'email' => 'newteacher@example.com',
            'role_id' => $teacherRole->id,
        ]);
    }

    public function test_teacher_can_edit_own_profile(): void
    {
        $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

        $this->actingAs($teacher);

        Livewire::test(EditUser::class, [
            'record' => $teacher->id,
        ])
            ->fillForm([
                'name' => 'Updated Name',
                'email' => $teacher->email,
                'role_id' => $teacher->role_id,
            ])
            ->call('save')
            ->assertNotified();

        $teacher->refresh();
        $this->assertEquals('Updated Name', $teacher->name);
    }

    public function test_teacher_cannot_edit_other_users(): void
    {
        $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);
        $otherTeacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

        $this->actingAs($teacher);

        Livewire::test(EditUser::class, [
            'record' => $otherTeacher->id,
        ])
            ->assertForbidden();
    }
}
