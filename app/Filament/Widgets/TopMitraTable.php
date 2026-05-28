<?php

namespace App\Filament\Widgets;

use App\Models\Mitra;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopMitraTable extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Mitra dengan Lead Terbanyak';
    protected int | string | array $columnSpan = 'one';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Mitra::query()
                    ->withCount('leads')
                    ->orderByDesc('leads_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Mitra')
                    ->searchable(),
                Tables\Columns\TextColumn::make('domisili')
                    ->label('Domisili'),
                Tables\Columns\TextColumn::make('leads_count')
                    ->label('Jumlah Lead')
                    ->sortable()
                    ->badge()
                    ->color('primary'),
            ])
            ->paginated(false);
    }
}
