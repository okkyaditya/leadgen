<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopSupportLeadsTable extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Support dengan Lead Terbanyak';
    protected int | string | array $columnSpan = 4;
    protected static ?int $sort = 4;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'support')
                    ->withCount('leads')
                    ->orderByDesc('leads_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Support')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cabang')
                    ->label('Cabang'),
                Tables\Columns\TextColumn::make('leads_count')
                    ->label('Jumlah Lead')
                    ->sortable()
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}
