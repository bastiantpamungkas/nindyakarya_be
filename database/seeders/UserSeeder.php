<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admins = [
            [
                'name' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'pin' => Hash::make('000000'),
            ]
        ];

        foreach ($admins as $userData) {
            // Create or fetch the role
            $user = User::firstOrCreate(['name' => $userData['name'], 'email' => $userData['email'], 'password' => $userData['password'], 'pin' => $userData['pin']]);
            // Assign the role to the user
            $user->assignRole('Admin');
        }

        $officers = [
            [
                'name' => 'officer',
                'email' => 'officer@gmail.com',
                'password' => Hash::make('password'),
                'pin' => Hash::make('000000'),
            ]
        ];

        foreach ($officers as $userData) {
            // Create or fetch the role
            $user = User::firstOrCreate(['name' => $userData['name'], 'email' => $userData['email'], 'password' => $userData['password'], 'pin' => $userData['pin']]);
            // Assign the role to the user
            $user->assignRole('Officer');
        }

        $pm = [
            [
                'name' => 'pm',
                'email' => 'pm@gmail.com',
                'password' => Hash::make('password'),
                'pin' => Hash::make('000000'),
            ]
        ];

        foreach ($pm as $userData) {
            // Create or fetch the role
            $user = User::firstOrCreate(['name' => $userData['name'], 'email' => $userData['email'], 'password' => $userData['password'], 'pin' => $userData['pin']]);
            // Assign the role to the user
            $user->assignRole('PM');
        }

        $vpqhse = [
            [
                'name' => 'vpqhse',
                'email' => 'vpqhse@gmail.com',
                'password' => Hash::make('password'),
                'pin' => Hash::make('000000'),
            ]
        ];

        foreach ($vpqhse as $userData) {
            // Create or fetch the role
            $user = User::firstOrCreate(['name' => $userData['name'], 'email' => $userData['email'], 'password' => $userData['password'], 'pin' => $userData['pin']]);
            // Assign the role to the user
            $user->assignRole('VP QHSE');
        }
    }
}
