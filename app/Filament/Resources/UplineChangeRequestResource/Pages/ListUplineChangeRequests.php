<?php

namespace App\Filament\Resources\UplineChangeRequestResource\Pages;

use App\Filament\Resources\UplineChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUplineChangeRequests extends ListRecords
{
    protected static string $resource = UplineChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
