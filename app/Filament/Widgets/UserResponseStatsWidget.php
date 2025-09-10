<?php

namespace App\Filament\Widgets;

use App\Models\UserResponse;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserResponseStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        // Total responses
        $totalResponses = UserResponse::count();

        // Responses today
        $responsesToday = UserResponse::whereDate('completed_at', today())->count();

        // Risk level distribution - sesuai dengan model (hanya 2 kategori)
        $riskLevels = UserResponse::select('risk_level', DB::raw('count(*) as count'))
            ->groupBy('risk_level')
            ->pluck('count', 'risk_level')
            ->toArray();

        $lowRisk = $riskLevels['Rendah'] ?? 0;
        $mediumHighRisk = $riskLevels['Sedang-Tinggi'] ?? 0;

        // Average score
        $averageScore = UserResponse::avg('total_score') ?? 0;

        // Growth calculation (this month vs last month)
        $thisMonth = UserResponse::whereMonth('completed_at', now()->month)
            ->whereYear('completed_at', now()->year)
            ->count();

        $lastMonth = UserResponse::whereMonth('completed_at', now()->subMonth()->month)
            ->whereYear('completed_at', now()->subMonth()->year)
            ->count();

        $growthPercentage = $lastMonth > 0 ? (($thisMonth - $lastMonth) / $lastMonth) * 100 : 0;

        return [
            Stat::make('Total Hasil Skrining', $totalResponses)
                ->description($responsesToday . ' hari ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 6, $responsesToday]),

            Stat::make('Risiko Rendah', $lowRisk)
                ->description(round(($totalResponses > 0 ? ($lowRisk / $totalResponses) * 100 : 0), 1) . '% dari total')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Risiko Sedang-Tinggi', $mediumHighRisk)
                ->description(round(($totalResponses > 0 ? ($mediumHighRisk / $totalResponses) * 100 : 0), 1) . '% dari total')
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color('warning'),

            Stat::make('Rata-rata Skor', number_format($averageScore, 1))
                ->description('Skor maksimal: 21')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($averageScore <= 7 ? 'success' : ($averageScore <= 14 ? 'warning' : 'danger')),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }

    // Refresh setiap 30 detik
    protected static ?string $pollingInterval = '30s';
}
