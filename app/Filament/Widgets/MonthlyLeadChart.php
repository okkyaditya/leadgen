<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;

class MonthlyLeadChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Chart Total Leads Jan - Dec';
    protected static ?string $maxHeight = '275px';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 3;

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $currentYear = (int)now()->format('Y');
        $filters = [];
        // Generate last 5 years for filtering by Year
        for ($y = $currentYear; $y >= $currentYear - 4; $y--) {
            $filters[(string)$y] = (string)$y;
        }
        return $filters;
    }

    protected function getData(): array
    {
        $year = (int)($this->filter ?? now()->format('Y'));
        
        $data = [];
        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        
        for ($m = 1; $m <= 12; $m++) {
            $count = Lead::whereMonth('created_at', $m)
                ->whereYear('created_at', $year)
                ->count();
            $data[] = $count;
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
