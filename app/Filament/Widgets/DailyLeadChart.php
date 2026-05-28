<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class DailyLeadChart extends ChartWidget
{
    protected static ?string $heading = 'Daily Leads (Last 30 Days)';
    protected static ?string $maxHeight = '275px';
    protected int | string | array $columnSpan = 'one';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Lead::whereDate('created_at', $date)->count();
            $data[] = $count;
            $labels[] = now()->subDays($i)->format('d M');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Daily Leads Created',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'fill' => 'start',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.05)',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
