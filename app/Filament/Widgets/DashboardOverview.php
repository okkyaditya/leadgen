<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Mitra;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalManagers = User::where('role', 'manager')->count();
        $totalSupervisors = User::where('role', 'supervisor')->count();
        $totalSupports = User::where('role', 'support')->count();
        $totalMitras = Mitra::count();
        
        $monthlyNtf = Lead::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('ntf');

        return [
            Stat::make('Total Manager', $totalManagers)
                ->description('Total akun internal Manager')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),
            Stat::make('Total Supervisor', $totalSupervisors)
                ->description('Total akun internal Supervisor')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
            Stat::make('Total Support', $totalSupports)
                ->description('Total akun internal Support')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),
            Stat::make('Total Mitra', $totalMitras)
                ->description('Total mitra eksternal terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Monthly Potential NTF', 'Rp ' . number_format($monthlyNtf, 0, ',', '.'))
                ->description('Estimasi NTF bulan berjalan')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
