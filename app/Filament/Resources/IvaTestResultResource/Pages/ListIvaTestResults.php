<?php

namespace App\Filament\Resources\IvaTestResultResource\Pages;

use App\Filament\Resources\IvaTestResultResource;
use App\Models\IvaTestResult;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListIvaTestResults extends ListRecords
{
    protected static string $resource = IvaTestResultResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Hasil Tes'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Semua')
                ->badge(IvaTestResult::count()),

            'positive' => Tab::make('Positif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('result', 'positif'))
                ->badge(IvaTestResult::where('result', 'positif')->count())
                ->badgeColor('danger'),

            'negative' => Tab::make('Negatif')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('result', 'negatif'))
                ->badge(IvaTestResult::where('result', 'negatif')->count())
                ->badgeColor('success'),

            'recent' => Tab::make('7 Hari Terakhir')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7)))
                ->badge(IvaTestResult::where('created_at', '>=', now()->subDays(7))->count())
                ->badgeColor('info'),

            'this_month' => Tab::make('Bulan Ini')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereMonth('examination_date', now()->month)
                    ->whereYear('examination_date', now()->year))
                ->badge(IvaTestResult::whereMonth('examination_date', now()->month)
                    ->whereYear('examination_date', now()->year)->count())
                ->badgeColor('warning'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            IvaTestResultResource\Widgets\IvaTestResultStatsOverview::class,
        ];
    }
}