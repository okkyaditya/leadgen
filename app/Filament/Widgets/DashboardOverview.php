<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Mitra;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalMitra = Mitra::count();
        $mitraAktif = Mitra::where('is_active', true)->count();
        $totalUser = User::whereIn('role', ['manager', 'supervisor', 'support'])->count();
        $potentialNtf = Lead::sum('ntf');
        $ticketSize = Lead::avg('ntf') ?? 0;

        return [
            Stat::make('Total Mitra', $totalMitra)
                ->description('Total mitra aktif & non aktif')
                ->icon('heroicon-m-user-group')
                ->color('primary'),
            Stat::make('Mitra Aktif', $mitraAktif)
                ->description('Total mitra aktif')
                ->icon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Total User', $totalUser)
                ->description('Manager + Supervisor + Support')
                ->icon('heroicon-m-users')
                ->color('info'),
            Stat::make('Potential NTF', 'Rp ' . number_format($potentialNtf, 0, ',', '.'))
                ->description('Akumulasi NTF Leads')
                ->icon('heroicon-m-currency-dollar')
                ->color('warning'),
            Stat::make('Ticket Size', 'Rp ' . number_format($ticketSize, 0, ',', '.'))
                ->description('Rata-rata NTF Leads')
                ->icon('heroicon-m-chart-bar')
                ->color('success'),
        ];
    }
}
