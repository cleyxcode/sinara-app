<?php

namespace App\Filament\Widgets;

use App\Models\UserResponse;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class RiskDistributionChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Distribusi Tingkat Risiko';
    
    protected static ?int $sort = 3;
    
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        $riskData = UserResponse::select('risk_level', DB::raw('count(*) as count'))
            ->groupBy('risk_level')
            ->pluck('count', 'risk_level')
            ->toArray();

        $lowRisk = $riskData['Rendah'] ?? 0;
        $mediumRisk = $riskData['Sedang'] ?? 0;
        $highRisk = $riskData['Tinggi'] ?? 0;

        return [
            'datasets' => [
                [
                    'data' => [$lowRisk, $mediumRisk, $highRisk],
                    'backgroundColor' => [
                        '#10b981', // Green for low risk
                        '#f59e0b', // Yellow for medium risk  
                        '#ef4444', // Red for high risk
                    ],
                    'borderColor' => [
                        '#059669',
                        '#d97706',
                        '#dc2626',
                    ],
                    'borderWidth' => 2,
                ],
            ],
            'labels' => ['Risiko Rendah', 'Risiko Sedang', 'Risiko Tinggi'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
            'maintainAspectRatio' => false,
        ];
    }
}