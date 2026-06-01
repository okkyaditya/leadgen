<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;
use Faker\Factory as Faker;
use Carbon\Carbon;

class LeadMonthlyDummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $users = User::where('role', '!=', 'mitra')->get();
        $mitras = User::where('role', 'mitra')->get();

        if ($mitras->isEmpty()) {
            $this->command->error('No Mitra found in database! Please seed Mitra first.');
            return;
        }

        $produkList = ['NDF Car', 'NDF Motor', 'NDF Property', 'Machinery', 'Heavy Equipment', 'DF Mobil', 'DF Motor'];
        
        $carUnits = ['Toyota Avanza', 'Honda Brio', 'Daihatsu Xenia', 'Suzuki Ertiga', 'Mitsubishi Xpander', 'Toyota Innova'];
        $motorUnits = ['Honda Beat', 'Yamaha Nmax', 'Honda Vario', 'Yamaha Mio', 'Suzuki Satria'];
        $propertyUnits = ['Rumah Type 36', 'Ruko 2 Lantai', 'Tanah Kavling', 'Apartemen Studio'];
        $machineryUnits = ['Mesin Bubut CNC', 'Genset 50KVA', 'Mesin Cetak Offset'];
        $heavyUnits = ['Excavator Komatsu', 'Forklift Toyota 3T', 'Bulldozer Caterpilar'];

        // Define target dates: April 2026 and May 2026
        $startDate = Carbon::create(2026, 4, 1);
        $endDate = Carbon::create(2026, 5, 31);

        $totalLeadsCreated = 0;
        $currentDate = $startDate->copy();

        $this->command->info('Generating daily leads for April 2026 and May 2026...');

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            // Generate a random number of leads for each day (e.g. 2 to 5)
            $leadsPerDay = rand(2, 5);

            for ($j = 0; $j < $leadsPerDay; $j++) {
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

                $mitra = $mitras->random();
                $ownerType = 'App\Models\User';
                $ownerId = $mitra->id;
                $inputBy = $mitra->supervisor_id ?? ($users->isEmpty() ? 1 : $users->random()->id);
                $sourceMitraId = $mitra->id;

                // Randomize time for that day
                $leadDate = $currentDate->copy()->setHour(rand(8, 20))->setMinute(rand(0, 59))->setSecond(rand(0, 59));

                Lead::create([
                    'nama' => $faker->name,
                    'telepon' => '08' . rand(11, 99) . str_pad(rand(1000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'nik' => '3173' . str_pad(rand(100000, 9999999999), 12, '0', STR_PAD_LEFT),
                    'produk' => $produk,
                    'ntf' => $ntf,
                    'unit' => $unit,
                    'no_unit' => $no_unit,
                    'owner_type' => $ownerType,
                    'owner_id' => $ownerId,
                    'input_by' => $inputBy,
                    'source_mitra_id' => $sourceMitraId,
                    'created_at' => $leadDate,
                    'updated_at' => $leadDate,
                ]);

                $totalLeadsCreated++;
            }

            $currentDate->addDay();
        }

        $this->command->info("Finished! Created a total of {$totalLeadsCreated} leads across all days in April and May 2026.");
    }
}
