<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mitra;
use Carbon\Carbon;

class DeactivateMitraCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mitra:deactivate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivate Mitra who have not logged in for the past 3 months';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threeMonthsAgo = Carbon::now()->subMonths(3);

        $mitras = Mitra::where('is_active', true)
            ->where(function ($query) use ($threeMonthsAgo) {
                $query->where('last_login_at', '<', $threeMonthsAgo)
                      ->orWhereNull('last_login_at');
            })->get();

        $count = 0;
        foreach ($mitras as $mitra) {
            $mitra->update([
                'is_active' => false,
                'is_active_reason' => 'Sistem otomatis: Tidak ada aktivitas login sejak 3 bulan terakhir.',
            ]);
            $count++;
        }

        $this->info("Deactivated {$count} Mitra.");
    }
}
