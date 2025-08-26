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
        $teacherRole = Role::where('name', 'Teacher')->first();
        
        User::create([
            'role_id' => $teacherRole->id,
            'name' => 'John Smith',
            'email' => 'john@example.com',
            'password' => 'password',
        ]);
    }
}
