<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cabangs', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100)->unique();
            $table->timestamps();
        });

        // Insert initial branches list
        $defaultCabangs = [
            'Jakarta Barat',
            'Jakarta Timur',
            'Jakarta Pusat',
            'Jakarta Selatan',
            'Jakarta Utara',
            'Tangerang',
            'Bandung',
            'Karawang',
            'Bogor',
            'Semarang',
            'Surabaya',
            'Sidoarjo',
            'Malang',
            'Yogya'
        ];

        foreach ($defaultCabangs as $cabang) {
            DB::table('cabangs')->insert([
                'nama' => $cabang,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabangs');
    }
};
