<?php

namespace App\Filament\Resources\LeadResource\Widgets;

use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeadOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $query = Lead::query();
        $user = auth()->user();
        
        // Respect role scoping
        if ($user && $user->hasAnyRole(['supervisor', 'support'])) {
            $query->where(function($q) use ($user) {
                $q->where('input_by', $user->id)
                  ->orWhereHas('sourceMitra', function($q2) use ($user) {
                      $q2->where('upline_id', $user->id);
                  });
            });
        }

        $totalLeads = (clone $query)->count();
        $leadsThisMonth = (clone $query)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $totalNtf = (clone $query)->sum('ntf');

        return [
            Stat::make('Total Lead', $totalLeads)
                ->description('Total prospek terdaftar')
                ->icon('heroicon-m-document-text')
                ->color('primary'),
            Stat::make('Leads/Month', $leadsThisMonth)
                ->description('Prospek baru bulan ini')
                ->icon('heroicon-m-calendar-days')
                ->color('info'),
            Stat::make('Potensial NTF', 'Rp ' . number_format($totalNtf, 0, ',', '.'))
                ->description('Akumulasi nilai transaksi')
                ->icon('heroicon-m-currency-dollar')
                ->color('success'),
        ];
    }
}
