<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lead;
use App\Models\User;

class DummyLeadSeeder extends Seeder
{
    private array $depan = [
        'Agus', 'Bambang', 'Cinta', 'Desi', 'Endang', 'Ferry', 'Galih',
        'Hesti', 'Ivan', 'Jihan', 'Kemal', 'Linda', 'Maul', 'Nadia',
        'Opik', 'Putra', 'Reza', 'Sari', 'Tuti', 'Udin', 'Vivi',
        'Wawan', 'Yuni', 'Zidan', 'Asep', 'Bagas', 'Cici', 'Doni',
        'Erna', 'Faisal', 'Gunadi', 'Hilda', 'Ilham', 'Jefri', 'Karim',
        'Lukman', 'Mira', 'Nita', 'Odi', 'Pras', 'Rara', 'Soleh',
        'Tika', 'Ujang', 'Wibi', 'Yanto', 'Zaenab', 'Anton', 'Beni', 'Candra',
    ];

    private array $belakang = [
        'Setiabudi', 'Maharani', 'Pranoto', 'Saputri', 'Hermansyah',
        'Wirawan', 'Cahyadi', 'Lubis', 'Ginting', 'Simbolon', 'Tampubolon',
        'Situmorang', 'Halim', 'Tanuwijaya', 'Iskandar', 'Mahendra',
        'Purnama', 'Subagyo', 'Mahmud', 'Yulianti', 'Pangestu', 'Adriana',
        'Wahyuni', 'Suryana', 'Saputra', 'Hendrawan', 'Mulyadi', 'Permadi',
        'Sembiring', 'Napitupulu', 'Manurung', 'Sihombing', 'Panjaitan',
    ];

    private array $units = [
        'Toyota Avanza', 'Honda Brio', 'Daihatsu Xenia', 'Suzuki Ertiga',
        'Mitsubishi Xpander', 'Honda Beat', 'Yamaha NMAX', 'Honda PCX',
        'Yamaha Aerox', 'Toyota Innova', 'Honda HR-V', 'Excavator PC200',
        'Wheel Loader', 'Forklift 3T', 'Ruko 2 Lantai', 'Rumah Tinggal',
        'Dump Truck', 'Bulldozer D6', 'Honda Vario', 'Toyota Rush',
    ];

    public function run(): void
    {
        $mitras = User::where('role', 'mitra')->get();

        if ($mitras->isEmpty()) {
            $this->command->warn('⚠ Tidak ada user mitra. Jalankan DummyUserSeeder dulu.');
            return;
        }

        $products = Lead::PRODUCTS;
        $types    = Lead::LEAD_TYPES;
        $total    = 200;

        $this->command->info("Creating {$total} leads...");

        $nikBase  = 1101000000000001;
        $telpBase = 81300000001;

        for ($i = 0; $i < $total; $i++) {
            $mitra = $mitras->random();

            $produk = $products[array_rand($products)];
            $ntf = $this->ntfForProduct($produk);

            Lead::create([
                'nama'            => $this->depan[array_rand($this->depan)] . ' ' . $this->belakang[array_rand($this->belakang)],
                'telepon'         => '0' . (string) ($telpBase + $i),
                'nik'             => (string) ($nikBase + $i),
                'produk'          => $produk,
                'tipe_lead'       => $types[array_rand($types)],
                'ntf'             => $ntf,
                'unit'            => $this->units[array_rand($this->units)],
                'no_unit'         => 'UNIT-' . str_pad((string) ($i + 1), 5, '0', STR_PAD_LEFT),
                'owner_type'      => User::class,
                'owner_id'        => $mitra->id,
                'input_by'        => $mitra->id,
                'source_mitra_id' => $mitra->id,
                'cabang'          => $mitra->cabang,
                'domisili'        => $mitra->cabang,
            ]);
        }

        $this->command->info("✅ {$total} leads created & assigned to mitra.");
    }

    private function ntfForProduct(string $produk): float
    {
        return match ($produk) {
            'NDF Motor', 'DF Motor'   => rand(8, 35) * 1_000_000,
            'NDF Car', 'DF Mobil'     => rand(80, 400) * 1_000_000,
            'NDF Property'            => rand(300, 2000) * 1_000_000,
            'Machinery'               => rand(150, 800) * 1_000_000,
            'Heavy Equipment'         => rand(500, 3000) * 1_000_000,
            default                   => rand(10, 100) * 1_000_000,
        };
    }
}
