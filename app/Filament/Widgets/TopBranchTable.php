<?php

namespace App\Filament\Widgets;

use App\Models\Lead;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TopBranchTable extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Branch by Lead';
    protected int | string | array $columnSpan = 'one';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Lead::query()
                    ->select('cabang', DB::raw('count(*) as count'))
                    ->whereNotNull('cabang')
                    ->where('cabang', '!=', '')
                    ->groupBy('cabang')
                    ->orderByDesc('count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('cabang')
                    ->label('Cabang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('count')
                    ->label('Jumlah Lead')
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
