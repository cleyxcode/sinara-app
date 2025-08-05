<?php

namespace App\Filament\Widgets;

use App\Models\UserApp;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserAppStatsWidget extends BaseWidget

{
    protected static ?int $sort = 4;
    protected function getStats(): array
    {
        // Total users
        $totalUsers = UserApp::count();
        
        // New users today
        $newUsersToday = UserApp::whereDate('created_at', today())->count();
        
        // Verified users
        $verifiedUsers = UserApp::whereNotNull('email_verified_at')->count();
        $verificationRate = $totalUsers > 0 ? ($verifiedUsers / $totalUsers) * 100 : 0;
        
        // Users this week vs last week
        $thisWeek = UserApp::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        
        $lastWeek = UserApp::whereBetween('created_at', [
            now()->subWeek()->startOfWeek(),
            now()->subWeek()->endOfWeek()
        ])->count();
        
        $weeklyGrowth = $lastWeek > 0 ? (($thisWeek - $lastWeek) / $lastWeek) * 100 : 0;
        
        // Daily registrations for the past 7 days (for chart)
        $dailyRegistrations = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dailyRegistrations[] = UserApp::whereDate('created_at', $date)->count();
        }

        return [
            Stat::make('Total Pengguna', number_format($totalUsers))
                ->description($newUsersToday . ' registrasi hari ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success')
                ->chart($dailyRegistrations),
            
            Stat::make('Email Terverifikasi', number_format($verifiedUsers))
                ->description(number_format($verificationRate, 1) . '% dari total pengguna')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($verificationRate >= 70 ? 'success' : ($verificationRate >= 40 ? 'warning' : 'danger')),
            
            Stat::make('Minggu Ini', $thisWeek)
                ->description('vs ' . $lastWeek . ' minggu lalu')
                ->descriptionIcon($weeklyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weeklyGrowth >= 0 ? 'success' : 'danger'),
            
            Stat::make('Pertumbuhan Mingguan', ($weeklyGrowth >= 0 ? '+' : '') . number_format($weeklyGrowth, 1) . '%')
                ->description('Perubahan registrasi')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color($weeklyGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
    
    protected static ?string $pollingInterval = '30s';
}