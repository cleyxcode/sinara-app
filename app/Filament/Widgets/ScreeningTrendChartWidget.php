<?php

namespace App\Filament\Widgets;

use App\Models\UserResponse;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ScreeningTrendChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Skrining 30 Hari Terakhir';
    
    protected static ?int $sort = 5;
    
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // Get data for the last 30 days
        $data = UserResponse::selectRaw('DATE(completed_at) as date, COUNT(*) as count')
            ->where('completed_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Create array of last 30 days
        $dates = [];
        $counts = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dates[] = now()->subDays($i)->format('d/m');
            
            // Find count for this date
            $dayData = $data->where('date', $date)->first();
            $counts[] = $dayData ? $dayData->count : 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Skrining',
                    'data' => $counts,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
    
    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}