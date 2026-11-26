<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TestingUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@myfinance.local'],
            [
                'name' => 'MyFinance Testing',
                'password' => Hash::make('Andi#230557'),
            ]
        );
    }
}
