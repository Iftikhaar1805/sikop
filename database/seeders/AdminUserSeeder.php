<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminName = config('koperasi.admin.name');
        $adminEmail = config('koperasi.admin.email');
        $adminPassword = config('koperasi.admin.password');

        if (! $adminName || ! $adminEmail || ! $adminPassword) {
            throw new RuntimeException('Set ADMIN_NAME, ADMIN_EMAIL, dan ADMIN_PASSWORD di .env sebelum menjalankan seeder admin.');
        }

        User::updateOrCreate(
            ['email' => $adminEmail],
            [
                'name' => $adminName,
                'password' => Hash::make($adminPassword),
                'role' => 'super_admin',
            ]
        );
    }
}
