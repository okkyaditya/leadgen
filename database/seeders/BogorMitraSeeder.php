<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cabang;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class BogorMitraSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Create Bogor branch if not exists
        $cabang = Cabang::firstOrCreate(['nama' => 'Bogor']);
        $this->command->info('Branch Bogor initialized.');

        // 2. Create a Supervisor for Bogor
        $supervisor = User::where('role', 'supervisor')->where('cabang', 'Bogor')->first();
        if (!$supervisor) {
            $supervisor = User::create([
                'nama' => 'Supervisor Bogor',
                'nik' => '2020' . str_pad(99, 12, '0', STR_PAD_LEFT),
                'telepon' => '081399999999',
                'email' => 'supervisor.bogor@salestracker.com',
                'role' => 'supervisor',
                'cabang' => 'Bogor',
                'password' => Hash::make('password'),
                'is_active' => true,
                'hire_date' => now(),
            ]);
            $supervisor->assignRole('supervisor');
            $this->command->info('Supervisor Bogor created (supervisor.bogor@salestracker.com / password)');
        }

        // 3. Create a Support user for Bogor
        $support = User::where('role', 'support')->where('cabang', 'Bogor')->first();
        if (!$support) {
            $support = User::create([
                'nama' => 'Support Bogor',
                'nik' => '3030' . str_pad(99, 12, '0', STR_PAD_LEFT),
                'telepon' => '081499999999',
                'email' => 'support.bogor@salestracker.com',
                'role' => 'support',
                'cabang' => 'Bogor',
                'password' => Hash::make('password'),
                'is_active' => true,
                'hire_date' => now(),
            ]);
            $support->assignRole('support');
            $this->command->info('Support Bogor created (support.bogor@salestracker.com / password)');
        }

        // 4. Create 13 Mitra under Support Bogor
        $profesiList = ['Karyawan Swasta', 'Wirausaha', 'PNS', 'Ibu Rumah Tangga', 'Mahasiswa', 'Freelance'];
        $this->command->info('Creating 13 Mitras in Bogor branch...');
        
        for ($i = 1; $i <= 13; $i++) {
            $user = User::create([
                'nama' => $faker->name,
                'nik' => '909099' . str_pad($i, 10, '0', STR_PAD_LEFT),
                'telepon' => '089999' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'email' => 'mitra.bogor' . $i . '@salestracker.com',
                'role' => 'mitra',
                'cabang' => 'Bogor',
                'password' => Hash::make('password'),
                'profesi' => $faker->randomElement($profesiList),
                'tanggal_lahir' => now()->subYears(rand(20, 50))->subDays(rand(1, 365))->toDateString(),
                'domisili' => 'Bogor',
                'supervisor_id' => $support->id, // Upline Support Bogor
                'is_active' => true,
            ]);
            $this->command->info("Mitra {$i} created: {$user->email}");
        }

        $this->command->info('Successfully seeded 13 Mitras in Bogor!');
    }
}
