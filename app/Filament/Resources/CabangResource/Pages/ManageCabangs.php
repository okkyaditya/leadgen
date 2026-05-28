<?php

namespace App\Filament\Resources\CabangResource\Pages;

use App\Filament\Resources\CabangResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageCabangs extends ManageRecords
{
    protected static string $resource = CabangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
