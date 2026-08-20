<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@yabar.local'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'phone' => '08010000001',
            ]
        );
        $admin->assignRole('admin');

        $staff = [
            ['name' => 'Jane Doe', 'email' => 'staff@yabar.local', 'phone' => '08010000002'],
            ['name' => 'John Smith', 'email' => 'john@yabar.local', 'phone' => '08010000003'],
        ];

        foreach ($staff as $member) {
            $user = User::firstOrCreate(
                ['email' => $member['email']],
                [
                    'name' => $member['name'],
                    'password' => Hash::make('password'),
                    'phone' => $member['phone'],
                ]
            );
            $user->assignRole('staff');
        }
    }
}