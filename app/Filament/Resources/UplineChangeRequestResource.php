<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UplineChangeRequestResource\Pages;
use App\Models\UplineChangeRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UplineChangeRequestResource extends Resource
{
    protected static ?string $model = UplineChangeRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = 'Data';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'manager', 'supervisor']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Permohonan Perubahan Upline')
                    ->description('Formulir pengajuan perubahan upline untuk Mitra.')
                    ->schema([
                        Forms\Components\Select::make('mitra_id')
                            ->relationship('mitra', 'nama')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($context) => $context !== 'create'),
                        Forms\Components\Select::make('new_upline_id')
                            ->relationship('newUpline', 'nama', fn (Builder $query) => $query->whereIn('role', ['supervisor', 'support']))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled(fn ($context) => $context !== 'create'),
                        Forms\Components\Hidden::make('requested_by')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->disabled(fn () => !auth()->user()->hasAnyRole(['admin', 'manager'])) // Only manager/admin can change status
                            ->required(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mitra.nama')->searchable(),
                Tables\Columns\TextColumn::make('newUpline.nama')->label('New Upline')->searchable(),
                Tables\Columns\TextColumn::make('status')->badge(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUplineChangeRequests::route('/'),
            'create' => Pages\CreateUplineChangeRequest::route('/create'),
            'edit' => Pages\EditUplineChangeRequest::route('/{record}/edit'),
        ];
    }
}
