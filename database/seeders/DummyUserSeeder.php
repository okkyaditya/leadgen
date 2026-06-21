<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DummyUserSeeder extends Seeder
{
    private array $depan = [
        'Andi', 'Budi', 'Citra', 'Dewi', 'Eka', 'Fajar', 'Gita', 'Hadi',
        'Indah', 'Joko', 'Kartika', 'Lestari', 'Maya', 'Nanda', 'Omar',
        'Putri', 'Rizky', 'Sari', 'Tono', 'Umi', 'Vina', 'Wahyu',
        'Yanti', 'Zainal', 'Aris', 'Bayu', 'Dani', 'Elin', 'Fadil',
        'Gina', 'Hendra', 'Ika', 'Juli', 'Koko', 'Lia', 'Marcel',
        'Nina', 'Oki', 'Pandu', 'Rina', 'Surya', 'Tari', 'Ucok',
        'Vera', 'Winda', 'Yoga', 'Zara', 'Adit', 'Bella', 'Cahya',
        'Dimas', 'Ella', 'Firman', 'Gilang', 'Hana', 'Irfan', 'Jaya',
        'Kiki', 'Luki', 'Mita', 'Nopal', 'Olivia', 'Prabu', 'Rama',
        'Sinta', 'Teguh', 'Utami', 'Vincent', 'Wulan', 'Yusuf', 'Zaki',
        'Aldo', 'Bunga', 'Coki', 'Dian', 'Edo', 'Fitri', 'Guntur',
        'Heri', 'Iin', 'Januar', 'Kirana', 'Lulu', 'Mega', 'Novi',
        'Oscar', 'Poppy', 'Rudi', 'Silvi', 'Taufik', 'Umra', 'Vito',
        'Wati', 'Yandi', 'Zulfikar', 'Arief', 'Binar', 'Dodi',
    ];

    private array $belakang = [
        'Pratama', 'Putri', 'Saputra', 'Wijaya', 'Sari', 'Hidayat',
        'Setiawan', 'Lestari', 'Ramadhan', 'Pertiwi', 'Kusuma', 'Susanto',
        'Handayani', 'Nugroho', 'Widodo', 'Anggraini', 'Firmansyah',
        'Oktaviani', 'Santoso', 'Maulana', 'Indah', 'Permata', 'Hakim',
        'Melati', 'Utomo', 'Safitri', 'Ardiansyah', 'Rahmawati', 'Gunawan',
        'Novitasari', 'Budiman', 'Prasetyo', 'Syaputra', 'Aulia', 'Wibowo',
        'Azzahra', 'Mukti', 'Wardani', 'Aisyah', 'Sugiyarto', 'Rahayu',
        'Aditya', 'Nursiah', 'Pramana', 'Hermawan', 'Supriyadi', 'Kurniawan',
        'Susilawati', 'Arianto', 'Wijayanti', 'Suryono', 'Laksono', 'Purbasari',
        'Hartono', 'Nuraini', 'Wibisono', 'Satria', 'Putro', 'Yulianto',
        'Sugiarto', 'Widyaningrum', 'Nugraha', 'Suryadi', 'Kusnadi',
        'Hariyanto', 'Suharto', 'Widjaja', 'Purnomo', 'Sudiarto',
        'Rachmawati', 'Widyastuti', 'Herlambang', 'Nurhayati', 'Darmawan',
    ];

    private array $profesi = [
        'Wiraswasta', 'Karyawan Swasta', 'PNS', 'Guru', 'Dokter',
        'Mahasiswa', 'Buruh', 'Pedagang', 'Petani', 'Supir',
        'TNI/Polri', 'Freelancer', 'Ibu Rumah Tangga', 'Konsultan',
        'Teknisi', 'Operator', 'Sales', 'Marketing', 'Admin', 'Perawat',
    ];

    // 14 cabang sesuai migration create_cabangs_table
    private array $cabangList = [
        'Jakarta Barat', 'Jakarta Timur', 'Jakarta Pusat',
        'Jakarta Selatan', 'Jakarta Utara', 'Tangerang',
        'Bandung', 'Karawang', 'Bogor', 'Semarang',
        'Surabaya', 'Sidoarjo', 'Malang', 'Yogya',
    ];

    private int $nikCounter  = 3201000000000001;
    private int $telpCounter = 81200000001;
    private int $emailCounter = 1;

    public function run(): void
    {
        $password = Hash::make('password');

        // 3 admin (tidak cabang-spesifik)
        $this->command->info('Creating admins...');
        for ($i = 0; $i < 3; $i++) {
            $this->createUser('admin', 'Pusat', $password);
        }

        // Tiap cabang: 1 manager, 1 supervisor, 2 support
        // 14 cabang × 4 = 56 user struktural
        $this->command->info('Creating per-cabang structure (manager, supervisor, support)...');
        $supervisorPool = []; // ['supervisor_id' => X, 'support_id' => Y, 'cabang' => Z]

        foreach ($this->cabangList as $cabang) {
            $manager    = $this->createUser('manager',    $cabang, $password);
            $supervisor = $this->createUser('supervisor', $cabang, $password);
            $support1   = $this->createUser('support',   $cabang, $password, $supervisor->id);
            $support2   = $this->createUser('support',   $cabang, $password, $supervisor->id);

            $supervisorPool[] = [
                'cabang'      => $cabang,
                'upline_ids'  => [$support1->id, $support2->id],
            ];
        }

        // Sisa slot untuk mitra: 100 - 3 - 56 = 41 mitra minimum
        // Kita buat 41 mitra tersebar ke cabang secara round-robin
        $this->command->info('Creating mitra...');
        $mitraCount = 41;
        $cabangCount = count($this->cabangList);

        for ($i = 0; $i < $mitraCount; $i++) {
            $pool   = $supervisorPool[$i % $cabangCount];
            $cabang = $pool['cabang'];
            $upline = $pool['upline_ids'][array_rand($pool['upline_ids'])];
            $this->createUser('mitra', $cabang, $password, $upline);
        }

        $this->command->info('✅ 100 dummy users created (3 admin + 14×4 struktural + 41 mitra).');
    }

    private function createUser(
        string $role,
        string $cabang,
        string $password,
        ?int $supervisorId = null
    ): User {
        $nama  = $this->depan[array_rand($this->depan)] . ' ' . $this->belakang[array_rand($this->belakang)];
        $nik   = (string) $this->nikCounter++;
        $telp  = '0' . (string) $this->telpCounter++;
        $email = 'user' . $this->emailCounter++ . '@dummy.com';
        $prof  = $this->profesi[array_rand($this->profesi)];

        $data = [
            'nama'         => $nama,
            'nik'          => $nik,
            'telepon'      => $telp,
            'email'        => $email,
            'role'         => $role,
            'cabang'       => $cabang,
            'profesi'      => $prof,
            'password'     => $password,
            'is_active'    => true,
            'supervisor_id' => $supervisorId,
        ];

        $user = User::create($data);

        $this->command->info("  [{$role}] #{$user->id} {$nama} | {$cabang}");

        return $user;
    }
}
