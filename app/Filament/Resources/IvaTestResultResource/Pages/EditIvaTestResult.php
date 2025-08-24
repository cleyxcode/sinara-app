<?php

namespace App\Filament\Resources\IvaTestResultResource\Pages;

use App\Filament\Resources\IvaTestResultResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditIvaTestResult extends EditRecord
{
    protected static string $resource = IvaTestResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make()
                ->visible(fn (): bool => 
                    $this->record->created_at->diffInHours(now()) <= 1
                ),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Hasil tes berhasil diperbarui')
            ->body('Data hasil pemeriksaan IVA telah berhasil diperbarui.');
    }

    protected function beforeSave(): void
    {
        // Cek apakah masih bisa diedit (dalam 24 jam)
        if ($this->record->created_at->diffInHours(now()) > 24) {
            Notification::make()
                ->danger()
                ->title('Tidak dapat mengedit')
                ->body('Hasil pemeriksaan hanya dapat diubah dalam 24 jam setelah pengiriman.')
                ->persistent()
                ->send();
            
            $this->halt();
        }
    }
}