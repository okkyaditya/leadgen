<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
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
    protected $description = 'Deactivate Mitra who have not logged in for the past 30 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        $mitras = User::where('role', 'mitra')
            ->where('is_active', true)
            ->where(function ($query) use ($thirtyDaysAgo) {
                $query->where('last_login_at', '<', $thirtyDaysAgo)
                      ->orWhere(function ($q) use ($thirtyDaysAgo) {
                          $q->whereNull('last_login_at')
                            ->where('created_at', '<', $thirtyDaysAgo);
                      });
            })->get();

        $count = 0;
        foreach ($mitras as $mitra) {
            $mitra->update([
                'is_active' => false,
                'is_active_reason' => 'Tidak Ada Aktifitas Selama 30 Hari',
            ]);
            $count++;
        }

        $this->info("Deactivated {$count} Mitra.");
    }
}
