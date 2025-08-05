<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Puskesmas',
                'password' => Hash::make('admin'),
            ]
        );

        // Admin 2
        User::updateOrCreate(
            ['email' => 'admin2@gmail.com'],
            [
                'name' => 'Admin Kecamatan',
                'password' => Hash::make('admin2'),
            ]
        );

        // Admin 3
        User::updateOrCreate(
            ['email' => 'admin3@gmail.com'],
            [
                'name' => 'Admin Wilayah',
                'password' => Hash::make('admin3'),
            ]
        );

        // Admin 4
        User::updateOrCreate(
            ['email' => 'admin4@gmail.com'],
            [
                'name' => 'Admin Pelayanan',
                'password' => Hash::make('admin4'),
            ]
        );
    }
}
