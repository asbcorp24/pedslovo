<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@pedslovo.local'],
            ['name' => 'Администратор', 'password' => Hash::make('ChangeMe123!'), 'role' => 'admin', 'approved_at' => now()]
        );

        User::updateOrCreate(
            ['email' => 'teacher@pedslovo.local'],
            ['name' => 'Преподаватель', 'password' => Hash::make('ChangeMe123!'), 'role' => 'teacher', 'approved_at' => now()]
        );

        User::updateOrCreate(
            ['email' => 'student@pedslovo.local'],
            ['name' => 'Студент', 'password' => Hash::make('ChangeMe123!'), 'role' => 'student', 'approved_at' => now()]
        );
    }
}
