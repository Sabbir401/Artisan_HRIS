<?php

namespace Database\Seeders;

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
        $user = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => '1212',
        ]);
        $user1 = User::create([
            'name' => 'Kazi Sabbir Ahmed Opi',
            'email' => 'sabbir@artisanbn.com',
            'password' => '3343',
        ]);
    }
}
