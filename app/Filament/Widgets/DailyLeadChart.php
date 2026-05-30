<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class DailyLeadChart extends ChartWidget
{
    protected static ?string $heading = 'Lead by Daily Input All Branches';
    protected static ?string $maxHeight = '275px';
    protected int | string | array $columnSpan = 6;
    protected static ?int $sort = 2;

    public ?string $filter = null;

    protected function getFilters(): ?array
    {
        $filters = [];
        // Generate last 12 months for filtering by Year & Month
        for ($i = 0; $i < 12; $i++) {
            $date = now()->subMonths($i);
            $key = $date->format('Y-m');
            $label = $date->translatedFormat('F Y');
            $filters[$key] = $label;
        }
        return $filters;
    }

    protected function getData(): array
    {
        $filter = $this->filter ?? now()->format('Y-m');
        $parts = explode('-', $filter);
        $year = (int)$parts[0];
        $month = (int)$parts[1];

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        
        $data = [];
        $labels = [];
        
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $count = Lead::whereDate('created_at', $date)->count();
            $data[] = $count;
            $labels[] = (string)$day;
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
