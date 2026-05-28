<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CabangResource\Pages;
use App\Models\Cabang;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;

class CabangResource extends Resource
{
    protected static ?string $model = Cabang::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $pluralLabel = 'Cabang';
    protected static ?string $modelLabel = 'Cabang';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Cabang')
                    ->description('Kelola data kantor cabang perusahaan di sini.')
                    ->schema([
                        TextInput::make('nama')
                            ->label('Nama Cabang')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->label('Dibuat Pada')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageCabangs::route('/'),
        ];
    }
}
