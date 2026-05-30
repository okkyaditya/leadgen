<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MitraResource\Pages;
use App\Models\Mitra;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;

class MitraResource extends Resource
{
    protected static ?string $model = Mitra::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Master Data';

    public static function canAccess(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Mitra')
                    ->description('Lengkapi detail informasi untuk akun mitra ini.')
                    ->schema([
                        TextInput::make('nik')
                            ->required()
                            ->maxLength(16)
                            ->regex('/^[0-9]+$/')
                            ->validationMessages([
                                'regex' => 'NIK hanya boleh berisi angka.',
                            ])
                            ->unique(ignoreRecord: true),
                        TextInput::make('nama')->required()->maxLength(150),
                        TextInput::make('telepon')
                            ->required()
                            ->maxLength(16)
                            ->regex('/^[0-9]+$/')
                            ->validationMessages([
                                'regex' => 'Nomor telepon hanya boleh berisi angka.',
                            ])
                            ->unique(ignoreRecord: true),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(191)
                            ->unique(ignoreRecord: true),
                        TextInput::make('password')
                            ->password()
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        TextInput::make('profesi')->required()->maxLength(100),
                        DatePicker::make('tanggal_lahir')->required(),
                        TextInput::make('domisili')->required()->maxLength(150),
                        Select::make('upline_id')
                            ->relationship('upline', 'nama', fn (Builder $query) => $query->whereIn('role', ['supervisor', 'support']))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn () => auth()->user()->hasAnyRole(['supervisor', 'support']) ? auth()->id() : null)
                            ->disabled(fn () => auth()->user()->hasAnyRole(['supervisor', 'support']))
                            ->dehydrated(),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                        TextInput::make('is_active_reason')->maxLength(255)->disabled(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nik')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('telepon')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('upline.nama')->label('Upline')->searchable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('last_login_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
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

    public static function getWidgets(): array
    {
        return [
            MitraResource\Widgets\MitraOverview::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMitras::route('/'),
            'create' => Pages\CreateMitra::route('/create'),
            'edit' => Pages\EditMitra::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['upline']);
        
        $user = auth()->user();
        if ($user && $user->hasAnyRole(['supervisor', 'support'])) {
            $query->where('upline_id', $user->id);
        }
        
        return $query;
    }
}
