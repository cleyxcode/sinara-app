<?php
// File: app/Filament/Resources/UserResponseResource/Pages/ViewUserResponse.php

namespace App\Filament\Resources\UserResponseResource\Pages;

use App\Filament\Resources\UserResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewUserResponse extends ViewRecord
{
    protected static string $resource = UserResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            
        ];
    }
}