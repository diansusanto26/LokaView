<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();

        $newUsersToday = User::where('role', 'user')
            ->whereDate('created_at', $today)
            ->count();

        $newUsersThisWeek = User::where('role', 'user')
            ->whereBetween('created_at', [$weekStart, Carbon::now()])
            ->count();

        $totalUsers = User::where('role', 'user')->count();

        return [
            Stat::make('Pengguna Baru Hari Ini', $newUsersToday)
                ->description('Total hari ini')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),
            Stat::make('Pengguna Baru Minggu Ini', $newUsersThisWeek)
                ->description('Total minggu ini')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),

            Stat::make('Total Pengguna', $totalUsers)
                ->description('Semua pengguna terdaftar')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

        ];
    }
}
