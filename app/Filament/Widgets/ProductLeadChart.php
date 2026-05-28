<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ProductLeadChart extends ChartWidget
{
    protected static ?string $heading = 'Leads by Product';
    protected static ?string $maxHeight = '275px';
    protected int | string | array $columnSpan = 'one';

    protected function getData(): array
    {
        $products = Lead::select('produk', DB::raw('count(*) as count'))
            ->groupBy('produk')
            ->get();

        $data = $products->pluck('count')->toArray();
        $labels = $products->pluck('produk')->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Leads',
                    'data' => $data,
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#6b7280'
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
