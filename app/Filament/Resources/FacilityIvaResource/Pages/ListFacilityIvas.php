<?php

namespace App\Filament\Resources\FacilityIvaResource\Pages;

use App\Filament\Resources\FacilityIvaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFacilityIvas extends ListRecords
{
    protected static string $resource = FacilityIvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
