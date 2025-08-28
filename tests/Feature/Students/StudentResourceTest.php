<?php

namespace Tests\Feature\Students;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Students\Pages\EditStudent;
use App\Filament\Resources\Students\Pages\ListStudents;
use App\Models\Role;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class StudentResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles needed for tests
        Role::create(['name' => 'Owner']);
        Role::create(['name' => 'Admin']);
        Role::create(['name' => 'Teacher']);
    }

    public function test_owner_can_list_students(): void
    {
        /** @var \App\Models\User $owner */
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $students = Student::factory()->count(5)->create();

        $this->actingAs($owner);

        Livewire::test(ListStudents::class)
            ->assertCanSeeTableRecords($students)
            ->assertSuccessful();
    }

    public function test_admin_can_list_students(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

        $this->actingAs($admin);

        Livewire::test(ListStudents::class)
            ->assertSuccessful();
    }

    public function test_teacher_cannot_list_students(): void
    {
        /** @var \App\Models\User $teacher */
        $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

        $this->actingAs($teacher);

        Livewire::test(ListStudents::class)
            ->assertForbidden();
    }

    public function test_owner_can_search_students_by_name(): void
    {
        /** @var \App\Models\User $owner */
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $student1 = Student::factory()->create(['name' => 'John Doe']);
        $student2 = Student::factory()->create(['name' => 'Jane Smith']);

        $this->actingAs($owner);

        Livewire::test(ListStudents::class)
            ->searchTable('John')
            ->assertCanSeeTableRecords([$student1])
            ->assertCanNotSeeTableRecords([$student2]);
    }

    public function test_admin_can_search_students_by_email(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);
        $student1 = Student::factory()->create(['email' => 'john@example.com']);
        $student2 = Student::factory()->create(['email' => 'jane@example.com']);

        $this->actingAs($admin);

        Livewire::test(ListStudents::class)
            ->searchTable('john@example.com')
            ->assertCanSeeTableRecords([$student1])
            ->assertCanNotSeeTableRecords([$student2]);
    }

    public function test_owner_can_render_create_student_page(): void
    {
        /** @var \App\Models\User $owner */
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);

        $this->actingAs($owner);

        Livewire::test(CreateStudent::class)
            ->assertSuccessful()
            ->assertFormExists();
    }

    public function test_admin_can_render_create_student_page(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);

        $this->actingAs($admin);

        Livewire::test(CreateStudent::class)
            ->assertSuccessful()
            ->assertFormExists();
    }

    public function test_teacher_cannot_render_create_student_page(): void
    {
        /** @var \App\Models\User $teacher */
        $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);

        $this->actingAs($teacher);

        Livewire::test(CreateStudent::class)
            ->assertForbidden();
    }

    public function test_owner_can_render_edit_student_page(): void
    {
        /** @var \App\Models\User $owner */
        $owner = User::factory()->create(['role_id' => Role::where('name', 'Owner')->first()->id]);
        $student = Student::factory()->create();

        $this->actingAs($owner);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->assertSuccessful()
            ->assertFormExists()
            ->assertFormFieldExists('name')
            ->assertFormFieldExists('email');
    }

    public function test_admin_can_edit_student_form_displays_current_data(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create(['role_id' => Role::where('name', 'Admin')->first()->id]);
        $student = Student::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->actingAs($admin);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->assertSchemaStateSet([
                'name' => 'John Doe',
                'email' => 'john@example.com',
            ]);
    }

    public function test_teacher_cannot_edit_student(): void
    {
        /** @var \App\Models\User $teacher */
        $teacher = User::factory()->create(['role_id' => Role::where('name', 'Teacher')->first()->id]);
        $student = Student::factory()->create();

        $this->actingAs($teacher);

        Livewire::test(EditStudent::class, ['record' => $student->getRouteKey()])
            ->assertForbidden();
    }
}
