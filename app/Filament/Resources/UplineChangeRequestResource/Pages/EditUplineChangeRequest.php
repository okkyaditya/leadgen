<?php

namespace App\Filament\Resources\UplineChangeRequestResource\Pages;

use App\Filament\Resources\UplineChangeRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUplineChangeRequest extends EditRecord
{
    protected static string $resource = UplineChangeRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
