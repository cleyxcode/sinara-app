<?php

namespace App\Filament\Resources\UserAppResource\Pages;

use App\Filament\Resources\UserAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserApp extends ViewRecord
{
    protected static string $resource = UserAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
