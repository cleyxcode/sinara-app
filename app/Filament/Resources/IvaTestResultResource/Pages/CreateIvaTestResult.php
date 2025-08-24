<?php

namespace App\Filament\Resources\IvaTestResultResource\Pages;

use App\Filament\Resources\IvaTestResultResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateIvaTestResult extends CreateRecord
{
    protected static string $resource = IvaTestResultResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hasil tes berhasil disimpan')
            ->body('Data hasil pemeriksaan IVA telah berhasil ditambahkan.');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validasi apakah sudah ada hasil untuk user, tanggal, dan jenis pemeriksaan yang sama
        $existingResult = \App\Models\IvaTestResult::where('user_id', $data['user_id'])
            ->where('examination_date', $data['examination_date'])
            ->where('examination_type', $data['examination_type'])
            ->first();

        if ($existingResult) {
            Notification::make()
                ->danger()
                ->title('Data sudah ada')
                ->body('Pasien sudah memiliki hasil pemeriksaan untuk tanggal dan jenis pemeriksaan yang sama.')
                ->persistent()
                ->send();
            
            $this->halt();
        }

        return $data;
    }
}