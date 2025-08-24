<?php

namespace App\Filament\Resources\FacilityIvaResource\Pages;

use App\Filament\Resources\FacilityIvaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFacilityIva extends CreateRecord
{
    protected static string $resource = FacilityIvaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Fasilitas IVA berhasil ditambahkan';
    }

    public function getTitle(): string
    {
        return 'Tambah Fasilitas IVA';
    }
}