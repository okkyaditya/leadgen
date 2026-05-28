<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Master Data';
    
    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['admin', 'manager']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pengguna')
                    ->description('Lengkapi detail informasi untuk akun pengguna ini.')
                    ->schema([
                        TextInput::make('nama')->required()->maxLength(150),
                        TextInput::make('nik')
                            ->required()
                            ->maxLength(16)
                            ->regex('/^[0-9]+$/')
                            ->validationMessages([
                                'regex' => 'NIK hanya boleh berisi angka.',
                            ])
                            ->unique(ignoreRecord: true),
                        TextInput::make('telepon')
                            ->required()
                            ->maxLength(16)
                            ->regex('/^[0-9]+$/')
                            ->validationMessages([
                                'regex' => 'Nomor telepon hanya boleh berisi angka.',
                            ])
                            ->unique(ignoreRecord: true),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'manager' => 'Manager',
                                'supervisor' => 'Supervisor',
                                'support' => 'Support',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Forms\Set $set) {
                                $set('roles', [$state]); // sync spatie role array
                            }),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->required()
                            ->hidden(), // Hide spatie roles, sync manually behind the scenes
                        Select::make('cabang')
                            ->options(fn () => \App\Models\Cabang::pluck('nama', 'nama')->toArray())
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DatePicker::make('hire_date')
                            ->label('Hire Date')
                            ->required(),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('nik')->searchable(),
                Tables\Columns\TextColumn::make('telepon')->searchable(),
                Tables\Columns\TextColumn::make('role')->badge()->label('Role'),
                Tables\Columns\TextColumn::make('cabang')->searchable(),
                Tables\Columns\TextColumn::make('hire_date')->date()->label('Hire Date')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('cabang')
                    ->label('Cabang')
                    ->options(fn () => \App\Models\Cabang::pluck('nama', 'nama')->toArray())
                    ->searchable()
                    ->preload(),
            ])
            ->groups([
                Tables\Grouping\Group::make('role')
                    ->label('Role')
                    ->collapsible(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getWidgets(): array
    {
        return [
            UserResource\Widgets\UserOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (auth()->user()->hasRole('manager')) {
            $query->whereIn('role', ['supervisor', 'support']);
        }
        
        return $query;
    }
}
