<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $query = User::query();
        
        // Respect manager role scope
        if (auth()->user()->hasRole('manager')) {
            $query->whereIn('role', ['supervisor', 'support']);
        }
        
        $totalUsers = (clone $query)->count();
        $totalSupport = (clone $query)->where('role', 'support')->count();
        $totalSupervisor = (clone $query)->where('role', 'supervisor')->count();
        $totalManager = (clone $query)->where('role', 'manager')->count();

        return [
            Stat::make('Total User', $totalUsers)
                ->description('Total pengguna aktif di sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
            Stat::make('Total Support', $totalSupport)
                ->description('Pengguna dengan Role Support')
                ->descriptionIcon('heroicon-m-wrench-screwdriver')
                ->color('info'),
            Stat::make('Total Supervisor', $totalSupervisor)
                ->description('Pengguna dengan Role Supervisor')
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('warning'),
            Stat::make('Total Manager', $totalManager)
                ->description('Pengguna dengan Role Manager')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),
        ];
    }
}
