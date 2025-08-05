<?php

namespace App\Filament\Resources\UserResponseResource\Pages;

use App\Filament\Resources\UserResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUserResponse extends EditRecord
{
    protected static string $resource = UserResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
