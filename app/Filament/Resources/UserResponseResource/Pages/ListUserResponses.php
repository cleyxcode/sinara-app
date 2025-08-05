<?php

// File: app/Filament/Resources/UserResponseResource/Pages/ListUserResponses.php

namespace App\Filament\Resources\UserResponseResource\Pages;

use App\Filament\Resources\UserResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListUserResponses extends ListRecords
{
    protected static string $resource = UserResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tidak ada action create karena data hanya dari API
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge($this->getModel()::count()),
            
            'rendah' => Tab::make('Risiko Rendah')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('risk_level', 'Rendah'))
                ->badge($this->getModel()::where('risk_level', 'Rendah')->count())
                ->badgeColor('success'),
            
            'sedang' => Tab::make('Risiko Sedang')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('risk_level', 'Sedang'))
                ->badge($this->getModel()::where('risk_level', 'Sedang')->count())
                ->badgeColor('warning'),
            
            'tinggi' => Tab::make('Risiko Tinggi')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('risk_level', 'Tinggi'))
                ->badge($this->getModel()::where('risk_level', 'Tinggi')->count())
                ->badgeColor('danger'),
        ];
    }
}