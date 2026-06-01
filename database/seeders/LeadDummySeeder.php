<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use Faker\Factory as Faker;

class LeadDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $users = User::where('role', '!=', 'mitra')->get();
        $mitras = User::where('role', 'mitra')->get();

        if ($users->isEmpty()) {
            $this->command->error('No users found in database! Please run DummyDataSeeder first.');
            return;
        }

        $produkList = ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor'];
        
        $carUnits = ['Toyota Avanza', 'Honda Brio', 'Daihatsu Xenia', 'Suzuki Ertiga', 'Mitsubishi Xpander', 'Toyota Innova'];
        $motorUnits = ['Honda Beat', 'Yamaha Nmax', 'Honda Vario', 'Yamaha Mio', 'Suzuki Satria'];
        $propertyUnits = ['Rumah Type 36', 'Ruko 2 Lantai', 'Tanah Kavling', 'Apartemen Studio'];
        $machineryUnits = ['Mesin Bubut CNC', 'Genset 50KVA', 'Mesin Cetak Offset'];
        $heavyUnits = ['Excavator Komatsu', 'Forklift Toyota 3T', 'Bulldozer Caterpilar'];

        $this->command->info('Generating 215 dummy leads...');

        for ($i = 1; $i <= 215; $i++) {
            $isMitraOwner = (rand(1, 100) <= 60); // 60% probability lead owned by Mitra
            
            $produk = $faker->randomElement($produkList);
            $ntf = null;
            $unit = null;
            $no_unit = null;

            // Generate realistic values based on product type
            switch ($produk) {
                case 'NDF Car':
                case 'DF Mobil':
                    $ntf = rand(80, 450) * 1000000; // 80 jt - 450 jt
                    $unit = $faker->randomElement($carUnits);
                    $no_unit = 'B ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90));
                    break;
                case 'NDF Motor':
                case 'DF Motor':
                    $ntf = rand(10, 45) * 1000000; // 10 jt - 45 jt
                    $unit = $faker->randomElement($motorUnits);
                    $no_unit = 'B ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90));
                    break;
                case 'NDF Property':
                    $ntf = rand(250, 1500) * 1000000; // 250 jt - 1.5 M
                    $unit = $faker->randomElement($propertyUnits);
                    break;
                case 'Machinery':
                    $ntf = rand(100, 800) * 1000000; // 100 jt - 800 jt
                    $unit = $faker->randomElement($machineryUnits);
                    break;
                case 'Heavy Equipment':
                    $ntf = rand(500, 2500) * 1000000; // 500 jt - 2.5 M
                    $unit = $faker->randomElement($heavyUnits);
                    break;
            }

            if ($isMitraOwner && !$mitras->isEmpty()) {
                $mitra = $mitras->random();
                $ownerType = 'App\Models\User';
                $ownerId = $mitra->id;
                $inputBy = $mitra->supervisor_id ?? $users->random()->id;
                $sourceMitraId = $mitra->id;
            } else {
                $user = $users->random();
                $ownerType = 'App\Models\User';
                $ownerId = $user->id;
                $inputBy = $user->id;
                $sourceMitraId = null;
            }

            Lead::create([
                'nama' => $faker->name,
                'telepon' => '08' . rand(11, 99) . str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT),
                'nik' => '3173' . str_pad($i, 12, '0', STR_PAD_LEFT),
                'produk' => $produk,
                'ntf' => $ntf,
                'unit' => $unit,
                'no_unit' => $no_unit,
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'input_by' => $inputBy,
                'source_mitra_id' => $sourceMitraId,
                'created_at' => now()->subDays(rand(0, 90))->subHours(rand(0, 23)),
            ]);
        }

        $this->command->info('215 dummy leads successfully generated!');
    }
}
