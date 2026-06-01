<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use App\Models\Cabang;
use Faker\Factory as Faker;

class AddLeadsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        $cabangs = Cabang::all();

        if ($cabangs->isEmpty()) {
            $this->command->error('No branches found. Please seed branches first.');
            return;
        }

        // Get all active Mitras who have a supervisor/upline
        $mitras = User::where('role', 'mitra')
            ->whereNotNull('supervisor_id')
            ->where('is_active', true)
            ->get();

        if ($mitras->isEmpty()) {
            $this->command->error('No active Mitra with an upline found! Please seed Mitra first.');
            return;
        }

        $produkList = ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor'];
        $carUnits = ['Toyota Avanza', 'Honda Brio', 'Daihatsu Xenia', 'Suzuki Ertiga', 'Mitsubishi Xpander', 'Toyota Innova'];
        $motorUnits = ['Honda Beat', 'Yamaha Nmax', 'Honda Vario', 'Yamaha Mio', 'Suzuki Satria'];
        $propertyUnits = ['Rumah Type 36', 'Ruko 2 Lantai', 'Tanah Kavling', 'Apartemen Studio'];
        $machineryUnits = ['Mesin Bubut CNC', 'Genset 50KVA', 'Mesin Cetak Offset'];
        $heavyUnits = ['Excavator Komatsu', 'Forklift Toyota 3T', 'Bulldozer Caterpilar'];

        $this->command->info('Creating 159 dummy leads distributed across all branches...');

        for ($i = 1; $i <= 159; $i++) {
            // Select branch sequentially to guarantee perfect distribution
            $cabang = $cabangs->keyBy('id')->values()->get(($i - 1) % $cabangs->count());

            // Try to find a Mitra in this branch
            $mitra = User::where('role', 'mitra')
                ->whereNotNull('supervisor_id')
                ->where('is_active', true)
                ->where(function($q) use ($cabang) {
                    $q->where('cabang', $cabang->nama)
                      ->orWhereHas('upline', function($uq) use ($cabang) {
                          $uq->where('cabang', $cabang->nama);
                      });
                })
                ->inRandomOrder()
                ->first();

            // Fallback to any Mitra if none exists in this specific branch
            if (!$mitra) {
                $mitra = $mitras->random();
            }

            $produk = $faker->randomElement($produkList);
            $ntf = null;
            $unit = null;
            $no_unit = null;

            switch ($produk) {
                case 'NDF Car':
                case 'DF Mobil':
                    $ntf = rand(80, 450) * 1000000;
                    $unit = $faker->randomElement($carUnits);
                    $no_unit = 'B ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90));
                    break;
                case 'NDF Motor':
                case 'DF Motor':
                    $ntf = rand(10, 45) * 1000000;
                    $unit = $faker->randomElement($motorUnits);
                    $no_unit = 'B ' . rand(1000, 9999) . ' ' . chr(rand(65, 90)) . chr(rand(65, 90));
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

            Lead::create([
                'nama' => $faker->name,
                'telepon' => '08' . rand(11, 99) . str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT),
                'nik' => '3173' . str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT) . str_pad($i, 4, '0', STR_PAD_LEFT),
                'produk' => $produk,
                'ntf' => $ntf,
                'unit' => $unit,
                'no_unit' => $no_unit,
                'owner_type' => 'App\Models\User',
                'owner_id' => $mitra->id,
                'input_by' => $mitra->supervisor_id,
                'source_mitra_id' => $mitra->id,
                'cabang' => $cabang->nama,
                'domisili' => $mitra->domisili ?? $cabang->nama,
                'created_at' => now()->subDays(rand(0, 90))->subHours(rand(0, 23)),
            ]);
        }

        $this->command->info('Successfully added 159 leads across branches!');
    }
}
