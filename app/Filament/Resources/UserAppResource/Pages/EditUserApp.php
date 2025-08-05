<?php

namespace App\Filament\Resources\UserAppResource\Pages;

use App\Filament\Resources\UserAppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserApp extends EditRecord
{
    protected static string $resource = UserAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
