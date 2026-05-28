<?php

namespace App\Filament\Mitra\Resources;

use App\Filament\Mitra\Resources\LeadResource\Pages;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Lead')
                    ->description('Lengkapi detail data prospek/lead baru.')
                    ->schema([
                        TextInput::make('nama')->required()->maxLength(150),
                        TextInput::make('telepon')
                            ->required()
                            ->maxLength(16)
                            ->regex('/^[0-9]+$/')
                            ->validationMessages([
                                'regex' => 'Nomor telepon hanya boleh berisi angka.',
                            ]),
                        TextInput::make('nik')
                            ->required()
                            ->maxLength(16)
                            ->regex('/^[0-9]+$/')
                            ->validationMessages([
                                'regex' => 'NIK hanya boleh berisi angka.',
                            ]),
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
                        TextInput::make('cabang')
                            ->label('Cabang')
                            ->default(fn () => auth()->user()->upline?->cabang ?? null)
                            ->disabled()
                            ->dehydrated(),
                        TextInput::make('domisili')
                            ->label('Domisili')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\Hidden::make('owner_type')
                            ->default('mitra'),
                        Forms\Components\Hidden::make('owner_id')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Hidden::make('source_mitra_id')
                            ->default(fn () => auth()->id()),
                        Forms\Components\Hidden::make('input_by')
                            ->default(fn () => auth()->user()->upline_id),
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
            'index' => Pages\ListLeads::route('/'),
            'create' => Pages\CreateLead::route('/create'),
            'edit' => Pages\EditLead::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        $user = auth()->user();
        if ($user) {
            $query->where('owner_type', 'mitra')
                  ->where('owner_id', $user->id);
        }
        
        return $query;
    }
}
