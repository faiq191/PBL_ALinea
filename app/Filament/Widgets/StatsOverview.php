<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Book;
use App\Models\Discussion;
use App\Models\BookReport;
use App\Models\UserReport;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $pendingBookReportsCount = BookReport::where('status', 'pending')->count();
        $pendingUserReportsCount = UserReport::where('status', 'pending')->count();
        $totalPendingReports = $pendingBookReportsCount + $pendingUserReportsCount;

        return [
            Stat::make('Total Pengguna', User::count())
                ->description('Pengguna terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
            Stat::make('Total Buku', Book::count())
                ->description('Buku dalam katalog')
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
            Stat::make('Diskusi Komunitas', Discussion::count())
                ->description('Topik diskusi aktif')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('info'),
            Stat::make('Laporan Tertunda', $totalPendingReports)
                ->description("{$pendingBookReportsCount} buku, {$pendingUserReportsCount} pengguna")
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($totalPendingReports > 0 ? 'danger' : 'success'),
        ];
    }
}
