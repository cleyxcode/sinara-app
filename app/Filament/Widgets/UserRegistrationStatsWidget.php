<?php

namespace App\Filament\Widgets;

use App\Models\UserApp;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class UserRegistrationStatsWidget extends BaseWidget
{
    
    protected function getStats(): array
    {
        // Total registered users
        $totalUsers = UserApp::count();
        
        // New users today
        $newUsersToday = UserApp::whereDate('created_at', today())->count();
        
        // New users this week
        $newUsersThisWeek = UserApp::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
        
        // New users this month
        $newUsersThisMonth = UserApp::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Previous month for comparison
        $newUsersLastMonth = UserApp::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        // Growth percentage
        $monthlyGrowth = $newUsersLastMonth > 0 ? 
            (($newUsersThisMonth - $newUsersLastMonth) / $newUsersLastMonth) * 100 : 0;
        
        // Email verification stats
        $verifiedUsers = UserApp::whereNotNull('email_verified_at')->count();
        $verificationRate = $totalUsers > 0 ? ($verifiedUsers / $totalUsers) * 100 : 0;
        
        // Daily registrations for the past 7 days (for chart)
        $dailyRegistrations = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dailyRegistrations[] = UserApp::whereDate('created_at', $date)->count();
        }

        return [
            Stat::make('Total Pengguna Terdaftar', number_format($totalUsers))
                ->description($newUsersToday . ' pendaftar hari ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('primary')
                ->chart($dailyRegistrations),
            
            Stat::make('Pendaftar Minggu Ini', $newUsersThisWeek)
                ->description('Registrasi 7 hari terakhir')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('success'),
            
            Stat::make('Pendaftar Bulan Ini', $newUsersThisMonth)
                ->description(($monthlyGrowth >= 0 ? '+' : '') . number_format($monthlyGrowth, 1) . '% vs bulan lalu')
                ->descriptionIcon($monthlyGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($monthlyGrowth >= 0 ? 'success' : 'danger'),
            
            Stat::make('Email Terverifikasi', number_format($verifiedUsers))
                ->description(number_format($verificationRate, 1) . '% dari total pengguna')
                ->descriptionIcon('heroicon-m-shield-check')
                ->color($verificationRate >= 70 ? 'success' : ($verificationRate >= 40 ? 'warning' : 'danger')),
        ];
    }
    
    protected function getColumns(): int
    {
        return 4;
    }
    
    protected static ?string $pollingInterval = '30s';
    
    protected static ?int $sort = 1; // Tampilkan di urutan pertama
}