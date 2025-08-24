<?php

namespace App\Filament\Resources\IvaTestResultResource\Widgets;

use App\Models\IvaTestResult;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class IvaTestResultStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalResults = IvaTestResult::count();
        $positiveResults = IvaTestResult::where('result', 'positif')->count();
        $negativeResults = IvaTestResult::where('result', 'negatif')->count();
        $thisMonthResults = IvaTestResult::whereMonth('examination_date', now()->month)
            ->whereYear('examination_date', now()->year)
            ->count();
        $lastMonthResults = IvaTestResult::whereMonth('examination_date', now()->subMonth()->month)
            ->whereYear('examination_date', now()->subMonth()->year)
            ->count();

        $monthlyChange = $lastMonthResults > 0 
            ? (($thisMonthResults - $lastMonthResults) / $lastMonthResults) * 100 
            : 0;

        $positiveRate = $totalResults > 0 ? ($positiveResults / $totalResults) * 100 : 0;

        return [
            Stat::make('Total Pemeriksaan', $totalResults)
                ->description($thisMonthResults . ' pemeriksaan bulan ini')
                ->descriptionIcon($monthlyChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyChange >= 0 ? 'success' : 'danger')
                ->chart($this->getMonthlyChart()),

            Stat::make('Hasil Positif', $positiveResults)
                ->description(number_format($positiveRate, 1) . '% dari total pemeriksaan')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($positiveRate > 20 ? 'danger' : ($positiveRate > 10 ? 'warning' : 'success')),

            Stat::make('Hasil Negatif', $negativeResults)
                ->description(number_format(100 - $positiveRate, 1) . '% dari total pemeriksaan')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Bulan Ini', $thisMonthResults)
                ->description($monthlyChange >= 0 ? 
                    '+' . number_format($monthlyChange, 1) . '% dari bulan lalu' : 
                    number_format($monthlyChange, 1) . '% dari bulan lalu')
                ->descriptionIcon($monthlyChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyChange >= 0 ? 'success' : 'danger'),
        ];
    }

    private function getMonthlyChart(): array
    {
        $data = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = IvaTestResult::whereMonth('examination_date', $date->month)
                ->whereYear('examination_date', $date->year)
                ->count();
            $data[] = $count;
        }

        return $data;
    }

    protected function getColumns(): int
    {
        return 4;
    }
}