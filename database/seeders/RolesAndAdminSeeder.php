<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::create(['name' => 'admin', 'guard_name' => 'web']);
        Role::create(['name' => 'manager', 'guard_name' => 'web']);
        Role::create(['name' => 'supervisor', 'guard_name' => 'web']);
        Role::create(['name' => 'support', 'guard_name' => 'web']);
        Role::create(['name' => 'mitra', 'guard_name' => 'web']);

        $admin = User::create([
            'nama' => 'Super Admin',
            'nik' => '1234567890123456',
            'telepon' => '081234567890',
            'email' => 'admin@salestracker.com',
            'role' => 'admin',
            'cabang' => 'Pusat',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');
    }
}
