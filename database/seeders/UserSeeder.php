<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ownerRole = Role::where('name', 'Owner')->first();
        $adminRole = Role::where('name', 'Admin')->first();
        $teacherRole = Role::where('name', 'Teacher')->first();
        
        // Create 1 owner
        User::factory()->create([
            'role_id' => $ownerRole->id,
            'name' => 'Business Owner',
            'email' => 'owner@example.com',
        ]);
        
        // Create 2 admins
        User::factory()->count(2)->create([
            'role_id' => $adminRole->id,
        ]);
        
        // Create 8 teachers
        User::factory()->count(8)->create([
            'role_id' => $teacherRole->id,
        ]);
    }
}
