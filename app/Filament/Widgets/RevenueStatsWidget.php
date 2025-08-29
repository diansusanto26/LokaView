<?php

namespace App\Filament\Widgets;

use App\Models\CoinTopUp;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {

        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();

        $revenueToday = CoinTopUp::where('status', 'success')
            ->whereDate('created_at', $today)
            ->sum('amount');

        $revenueThisWeek = CoinTopUp::where('status', 'success')
            ->whereBetween('created_at', [$weekStart, Carbon::now()])
            ->sum('amount');

        $topUpsToday = CoinTopUp::where('status', 'success')
            ->whereDate('created_at', $today)
            ->count();


        return [
            Stat::make('Pendapatan Hari Ini', 'Rp ' . number_format($revenueToday, 0, ',', '.'))
                ->description("Dari {$topUpsToday} top up")
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Pendapatan Minggu Ini', 'Rp ' . number_format($revenueThisWeek, 0, ',', '.'))
                ->description('Total minggu ini')
                ->descriptionIcon('heroicon-m-chart-bar-square')
                ->color('info'),

            Stat::make('Top Up Berhasil Hari Ini', $topUpsToday)
                ->description('Transaksi sukses')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),
        ];
    }
}
