<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrNew(['email' => 'admin@example.com']);

        $user->name = 'Admin';
        $user->password = Hash::make('ChangeMe123!');
        $user->role = 'admin';
        $user->save();
    }
}
