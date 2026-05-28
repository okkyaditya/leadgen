<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeadResource\Pages;
use App\Models\Lead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class LeadResource extends Resource
{
    protected static ?string $model = Lead::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Lead')
                    ->description('Lengkapi detail data prospek/lead baru.')
                    ->schema([
                        TextInput::make('nama')->required()->maxLength(150),
                        TextInput::make('telepon')->required()->maxLength(20),
                        TextInput::make('nik')->required()->maxLength(20),
                        Select::make('produk')
                            ->options([
                                'NDF Car' => 'NDF Car',
                                'NDF Motor' => 'NDF Motor',
                                'NDF Property' => 'NDF Property',
                                'Machinery' => 'Machinery',
                                'Heavy Equipment' => 'Heavy Equipment',
                                'DF Mobil' => 'DF Mobil',
                                'DF Motor' => 'DF Motor',
                            ])
                            ->required(),
                        TextInput::make('ntf')->numeric(),
                        TextInput::make('unit')->maxLength(100),
                        TextInput::make('no_unit')->maxLength(50),
                        Forms\Components\Hidden::make('owner_type')
                            ->default('user'),
                        Forms\Components\Hidden::make('owner_id')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Hidden::make('input_by')
                            ->default(fn () => auth()->id()),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('telepon')->searchable(),
                Tables\Columns\TextColumn::make('produk')->searchable(),
                Tables\Columns\TextColumn::make('ntf')->numeric(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cabang')
                    ->label('Cabang')
                    ->options(fn () => \App\Models\Cabang::pluck('nama', 'nama')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (! empty($data['value'])) {
                            $query->whereHas('inputBy', function ($q) use ($data) {
                                $q->where('cabang', $data['value']);
                            });
                        }
                    })
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('admin')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ExportBulkAction::make(),
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['supervisor', 'support'])) {
            $query->where(function($q) use ($user) {
                $q->where('input_by', $user->id)
                  ->orWhereHas('sourceMitra', function($q2) use ($user) {
                      $q2->where('upline_id', $user->id);
                  });
            });
        }
        
        return $query;
    }
}
