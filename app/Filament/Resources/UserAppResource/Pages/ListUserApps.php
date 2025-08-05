<?php

namespace App\Filament\Resources\UserAppResource\Pages;

use App\Filament\Resources\UserAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUserApps extends ListRecords
{
    protected static string $resource = UserAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada action create karena data dari API
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua Pengguna')
                ->badge(fn () => $this->getModel()::count()),
            
            'verified' => Tab::make('Email Terverifikasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('email_verified_at'))
                ->badge(fn () => $this->getModel()::whereNotNull('email_verified_at')->count()),
            
            'unverified' => Tab::make('Email Belum Terverifikasi')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('email_verified_at'))
                ->badge(fn () => $this->getModel()::whereNull('email_verified_at')->count()),
            
            'recent' => Tab::make('Registrasi Minggu Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                ->badge(fn () => $this->getModel()::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Anda bisa menambahkan widget statistik di sini
        ];
    }
}