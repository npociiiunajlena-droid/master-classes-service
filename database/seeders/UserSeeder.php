<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'full_name' => 'Иванова Ольга Ивановна',
                'email' => 'master.olga@example.com',
                'phone' => '+79001111111',
                'role' => 'master',
                'photo_path' => 'assets/img/driver1.png',
                'password' => Hash::make('password123'),
            ],
            [
                'full_name' => 'Смирнов Андрей Петрович',
                'email' => 'master.andrey@example.com',
                'phone' => '+79002222222',
                'role' => 'master',
                'photo_path' => 'assets/img/driver2.png',
                'password' => Hash::make('password123'),
            ],
            [
                'full_name' => 'Иванов Иван Иванович',
                'email' => 'visitor.ivan@example.com',
                'phone' => '+79003333333',
                'role' => 'visitor',
                'photo_path' => null,
                'password' => Hash::make('password123'),
            ],
            [
                'full_name' => 'Петрова Анна Сергеевна',
                'email' => 'visitor.anna@example.com',
                'phone' => '+79004444444',
                'role' => 'visitor',
                'photo_path' => null,
                'password' => Hash::make('password123'),
            ],
        ];

        foreach ($users as $userData) {
            User::query()->updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
