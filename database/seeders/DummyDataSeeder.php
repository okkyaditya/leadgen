<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        
        $cabangs = Cabang::pluck('nama')->toArray();
        if (empty($cabangs)) {
            $cabangs = ['Jakarta Barat', 'Jakarta Timur', 'Jakarta Pusat', 'Jakarta Selatan', 'Jakarta Utara'];
        }

        // Generate 10 Manager
        $this->command->info('Creating 10 Managers...');
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'nama' => $faker->name,
                'nik' => '1010' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'telepon' => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'manager' . $i . '@salestracker.com',
                'role' => 'manager',
                'cabang' => $faker->randomElement($cabangs),
                'password' => Hash::make('password'),
                'is_active' => true,
                'hire_date' => now()->subMonths(rand(1, 36)),
            ]);
            $user->assignRole('manager');
        }

        // Generate 10 Supervisor
        $this->command->info('Creating 10 Supervisors...');
        for ($i = 1; $i <= 10; $i++) {
            $user = User::create([
                'nama' => $faker->name,
                'nik' => '2020' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'telepon' => '0813' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'supervisor' . $i . '@salestracker.com',
                'role' => 'supervisor',
                'cabang' => $faker->randomElement($cabangs),
                'password' => Hash::make('password'),
                'is_active' => true,
                'hire_date' => now()->subMonths(rand(1, 36)),
            ]);
            $user->assignRole('supervisor');
        }

        // Generate 20 Support
        $this->command->info('Creating 20 Support users...');
        $supportIds = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'nama' => $faker->name,
                'nik' => '3030' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'telepon' => '0814' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'support' . $i . '@salestracker.com',
                'role' => 'support',
                'cabang' => $faker->randomElement($cabangs),
                'password' => Hash::make('password'),
                'is_active' => true,
                'hire_date' => now()->subMonths(rand(1, 36)),
            ]);
            $user->assignRole('support');
            $supportIds[] = $user->id;
        }

        // Generate 100 Mitra
        $this->command->info('Creating 100 Mitra...');
        $profesiList = ['Karyawan Swasta', 'Wirausaha', 'PNS', 'Ibu Rumah Tangga', 'Mahasiswa', 'Freelance'];
        for ($i = 1; $i <= 100; $i++) {
            User::create([
                'nama' => $faker->name,
                'nik' => '9090' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'telepon' => '0899' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'mitra' . $i . '@salestracker.com',
                'role' => 'mitra',
                'password' => Hash::make('password'),
                'profesi' => $faker->randomElement($profesiList),
                'tanggal_lahir' => now()->subYears(rand(20, 50))->subDays(rand(1, 365))->toDateString(),
                'domisili' => $faker->randomElement($cabangs),
                'supervisor_id' => $faker->randomElement($supportIds),
                'is_active' => true,
            ]);
        }

        $this->command->info('Dummy data successfully seeded!');
    }
}
