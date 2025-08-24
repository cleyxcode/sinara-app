<?php

namespace App\Filament\Resources\FacilityIvaResource\Pages;

use App\Filament\Resources\FacilityIvaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacilityIva extends EditRecord
{
    protected static string $resource = FacilityIvaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Fasilitas IVA berhasil diperbarui';
    }

    public function getTitle(): string
    {
        return 'Edit Fasilitas IVA';
    }
}