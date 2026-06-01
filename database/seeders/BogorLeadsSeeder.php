<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use App\Models\Cabang;
use Faker\Factory as Faker;

class BogorLeadsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Check if branch Bogor exists
        $cabang = Cabang::where('nama', 'Bogor')->first();
        if (!$cabang) {
            $this->command->error('Branch Bogor not found. Please run BogorMitraSeeder first.');
            return;
        }

        // Fetch Bogor branch users (Support & Supervisor)
        $bogorStaff = User::whereIn('role', ['support', 'supervisor'])
            ->where('cabang', 'Bogor')
            ->get();

        if ($bogorStaff->isEmpty()) {
            $this->command->error('No Support or Supervisor found in Bogor! Please run BogorMitraSeeder first.');
            return;
        }

        // Fetch Bogor Mitras
        $bogorMitras = User::where('role', 'mitra')
            ->where('cabang', 'Bogor')
            ->get();

        if ($bogorMitras->isEmpty()) {
            $this->command->error('No Mitra found in Bogor! Please run BogorMitraSeeder first.');
            return;
        }

        $produkList = ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor'];
        $carUnits = ['Toyota Avanza', 'Honda Brio', 'Daihatsu Xenia', 'Suzuki Ertiga', 'Mitsubishi Xpander', 'Toyota Innova'];
        $motorUnits = ['Honda Beat', 'Yamaha Nmax', 'Honda Vario', 'Yamaha Mio', 'Suzuki Satria'];
        $propertyUnits = ['Rumah Type 36', 'Ruko 2 Lantai', 'Tanah Kavling', 'Apartemen Studio'];
        $machineryUnits = ['Mesin Bubut CNC', 'Genset 50KVA', 'Mesin Cetak Offset'];
        $heavyUnits = ['Excavator Komatsu', 'Forklift Toyota 3T', 'Bulldozer Caterpilar'];

        $this->command->info('Creating 64 dummy leads in Bogor branch...');

        for ($i = 1; $i <= 64; $i++) {
            $isMitraOwner = (rand(1, 100) <= 75); // 75% chance lead is owned by a Bogor Mitra

            $produk = $faker->randomElement($produkList);
            $ntf = null;
            $unit = null;
            $no_unit = null;

            switch ($produk) {
                case 'NDF Car':
                case 'DF Mobil':
                    $ntf = rand(80, 450) * 1000000;
                    $unit = $faker->randomElement($carUnits);
                    $no_unit = 'F ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90)); // F is Bogor plate
                    break;
                case 'NDF Motor':
                case 'DF Motor':
                    $ntf = rand(10, 45) * 1000000;
                    $unit = $faker->randomElement($motorUnits);
                    $no_unit = 'F ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90));
                    break;
                case 'NDF Property':
                    $ntf = rand(250, 1500) * 1000000;
                    $unit = $faker->randomElement($propertyUnits);
                    break;
                case 'Machinery':
                    $ntf = rand(100, 800) * 1000000;
                    $unit = $faker->randomElement($machineryUnits);
                    break;
                case 'Heavy Equipment':
                    $ntf = rand(500, 2500) * 1000000;
                    $unit = $faker->randomElement($heavyUnits);
                    break;
            }

            if ($isMitraOwner) {
                $mitra = $bogorMitras->random();
                $ownerType = 'App\Models\User';
                $ownerId = $mitra->id;
                $inputBy = $mitra->supervisor_id ?? $bogorStaff->random()->id;
                $sourceMitraId = $mitra->id;
            } else {
                $staff = $bogorStaff->random();
                $ownerType = 'App\Models\User';
                $ownerId = $staff->id;
                $inputBy = $staff->id;
                $sourceMitraId = null;
            }

            Lead::create([
                'nama' => $faker->name,
                'telepon' => '08' . rand(11, 99) . str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT),
                'nik' => '3201' . str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT) . str_pad($i, 4, '0', STR_PAD_LEFT), // 3201 is Bogor NIK prefix
                'produk' => $produk,
                'ntf' => $ntf,
                'unit' => $unit,
                'no_unit' => $no_unit,
                'owner_type' => $ownerType,
                'owner_id' => $ownerId,
                'input_by' => $inputBy,
                'source_mitra_id' => $sourceMitraId,
                'cabang' => 'Bogor',
                'domisili' => 'Bogor',
                'created_at' => now()->subDays(rand(0, 90))->subHours(rand(0, 23)),
            ]);
        }

        $this->command->info('Successfully generated 64 leads for Bogor branch!');
    }
}
