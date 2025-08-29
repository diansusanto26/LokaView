<?php

namespace App\Filament\Widgets;

use App\Models\SeriesEpisode;
use Carbon\Carbon;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class PopularEpisodesWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Episode paling populer Minggu ini';

    public function table(Table $table): Table
    {
        $weekStart = Carbon::now()->startOfWeek();

        return $table
            ->query(
                SeriesEpisode::query()
                    ->withCount(['unlockedEpisodes as unlocks_this_week' => function (Builder $query) use ($weekStart) {
                        $query->whereBetween('created_at', [$weekStart, Carbon::now()]);
                    }])
                    ->with(['series'])
                    ->having('unlocks_this_week', '>', 0)
                    ->orderByDesc('unlocks_this_week')
                    ->limit(10)

            )
            ->columns([
                Tables\Columns\TextColumn::make('series.title')
                    ->label('Series')
                    ->limit(30)
                    ->tooltip(function (SeriesEpisode $record): string {
                        return $record->series->title;
                    }),

                Tables\Columns\TextColumn::make('episode_number')
                    ->label('Episode')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('Judul Episode')
                    ->limit(40)
                    ->tooltip(function (SeriesEpisode $record): string {
                        return $record->title;
                    }),

                Tables\Columns\TextColumn::make('unlock_cost')
                    ->label('Biaya Membuka')
                    ->formatStateUsing(fn($state) => $state . ' koin')
                    ->badge()
                    ->color('warning'),
            ])->paginated(false);
    }
}
