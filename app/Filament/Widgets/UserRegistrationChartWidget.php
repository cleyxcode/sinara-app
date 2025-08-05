<?php

namespace App\Filament\Widgets;

use App\Models\UserApp;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class UserRegistrationChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Trend Pendaftaran Pengguna (30 Hari Terakhir)';
    
    protected static ?int $sort = 2;
    
    protected static ?string $pollingInterval = '60s';

    protected function getData(): array
    {
        // Get registration data for the last 30 days
        $registrationData = UserApp::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Create array of last 30 days
        $dates = [];
        $counts = [];
        $cumulativeCounts = [];
        $totalSoFar = UserApp::where('created_at', '<', now()->subDays(30))->count();
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dates[] = now()->subDays($i)->format('d/m');
            
            // Find count for this date
            $dayData = $registrationData->where('date', $date)->first();
            $dailyCount = $dayData ? $dayData->count : 0;
            $counts[] = $dailyCount;
            
            // Calculate cumulative count
            $totalSoFar += $dailyCount;
            $cumulativeCounts[] = $totalSoFar;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendaftar Harian',
                    'data' => $counts,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Total Kumulatif',
                    'data' => $cumulativeCounts,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                    'type' => 'line',
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
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'beginAtZero' => true,
                    'title' => [
                        'display' => true,
                        'text' => 'Pendaftar Harian',
                    ],
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'title' => [
                        'display' => true,
                        'text' => 'Total Kumulatif',
                    ],
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }
}