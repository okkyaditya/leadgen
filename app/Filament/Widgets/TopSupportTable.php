<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopSupportTable extends BaseWidget
{
    protected static ?string $heading = 'Top 10 Support dengan Mitra Terbanyak';
    protected int | string | array $columnSpan = 'one';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                User::query()
                    ->where('role', 'support')
                    ->withCount('mitra')
                    ->orderByDesc('mitra_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Support')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cabang')
                    ->label('Cabang'),
                Tables\Columns\TextColumn::make('mitra_count')
                    ->label('Jumlah Mitra')
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false);
    }
}
