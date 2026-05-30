<?php

namespace App\Filament\Resources\MitraResource\Widgets;

use App\Models\Mitra;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class MitraOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Mitra::query();
        $user = auth()->user();
        
        // Respect role scoping
        if ($user && $user->hasAnyRole(['supervisor', 'support'])) {
            $query->where('upline_id', $user->id);
        }

        $totalMitra = (clone $query)->count();
        $activeMitra = (clone $query)->where('is_active', true)->count();
        
        $dates = (clone $query)->whereNotNull('tanggal_lahir')->pluck('tanggal_lahir');
        if ($dates->isEmpty()) {
            $averageAge = 0;
        } else {
            $totalAge = 0;
            foreach ($dates as $date) {
                $totalAge += Carbon::parse($date)->age;
            }
            $averageAge = round($totalAge / $dates->count(), 1);
        }

        return [
            Stat::make('Total Mitra', $totalMitra)
                ->description('Total mitra terdaftar')
                ->icon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Mitra Aktif', $activeMitra)
                ->description('Mitra dengan status aktif')
                ->icon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Rata-rata Usia', $averageAge . ' Tahun')
                ->description('Berdasarkan tanggal lahir')
                ->icon('heroicon-m-calendar')
                ->color('info'),
        ];
    }
}
