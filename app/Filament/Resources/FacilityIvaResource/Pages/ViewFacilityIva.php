<?php

namespace App\Filament\Resources\FacilityIvaResource\Pages;

use App\Filament\Resources\FacilityIvaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFacilityIva extends ViewRecord
{
    protected static string $resource = FacilityIvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function getTitle(): string
    {
        return 'Detail Fasilitas IVA';
    }
}