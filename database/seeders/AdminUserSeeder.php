<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // ── Owner Account ──
        $owner = User::create([
            'name'     => 'Owner Kios Adiva',
            'email'    => 'owner@kiosadiva.com',
            'password' => Hash::make('password'),
            'phone'    => '081234567890',
            'is_active'=> true,
            'email_verified_at' => now(),
        ]);
        $owner->assignRole('owner');

        // ── Admin Account ──
        $admin = User::create([
            'name'     => 'Admin Kios Adiva',
            'email'    => 'admin@kiosadiva.com',
            'password' => Hash::make('password'),
            'phone'    => '081234567891',
            'is_active'=> true,
            'email_verified_at' => now(),
        ]);
        $admin->assignRole('admin');

        // ── Kasir Account ──
        $kasir = User::create([
            'name'     => 'Kasir Kios Adiva',
            'email'    => 'kasir@kiosadiva.com',
            'password' => Hash::make('password'),
            'phone'    => '081234567892',
            'is_active'=> true,
            'email_verified_at' => now(),
        ]);
        $kasir->assignRole('kasir');
    }
}