<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class MonthlyLeadChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Leads (Current Year)';
    protected static ?string $maxHeight = '275px';
    protected int | string | array $columnSpan = 'one';

    protected function getData(): array
    {
        $data = [];
        $labels = [];
        
        for ($m = 1; $m <= 12; $m++) {
            $count = Lead::whereMonth('created_at', $m)->whereYear('created_at', now()->year)->count();
            $data[] = $count;
            $labels[] = date('M', mktime(0, 0, 0, $m, 1));
        }

        return [
            'datasets' => [
                [
                    'label' => 'Monthly Leads Created',
                    'data' => $data,
                    'backgroundColor' => '#f59e0b',
                    'borderRadius' => 4,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
