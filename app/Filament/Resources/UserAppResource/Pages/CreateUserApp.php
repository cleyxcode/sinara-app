<?php

namespace App\Filament\Resources\UserAppResource\Pages;

use App\Filament\Resources\UserAppResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUserApp extends CreateRecord
{
    protected static string $resource = UserAppResource::class;
}
