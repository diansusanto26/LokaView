<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnlockedEpisodeResource\Pages;
use App\Filament\Resources\UnlockedEpisodeResource\RelationManagers;
use App\Models\SeriesEpisode;
use App\Models\UnlockedEpisode;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnlockedEpisodeResource extends Resource
{
    protected static ?string $model = UnlockedEpisode::class;

    protected static ?string $navigationIcon = 'heroicon-o-lock-open';

    protected static ?string $navigationGroup = 'Manajemen User';

    protected static ?string $navigationLabel = 'Riwayat Unlock Episode';
    protected static ?string $pluralModelLabel = 'Riwayat Unlock Episode';

    protected static ?string $pluralLabel = 'Riwayat Unlock Episode';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seriesEpisode.series.title')
                    ->label('Series')
                    ->searchable(),
                Tables\Columns\TextColumn::make('seriesEpisode.episode_number')
                    ->label('Episode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Unlock')
                    ->dateTime('d-m-Y H:i:s')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('series_episode_id')
                    ->label('Series Episode')
                    ->options(SeriesEpisode::all()->pluck('title', 'id'))
                    ->searchable(),
            ])

            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnlockedEpisodes::route('/'),
            'create' => Pages\CreateUnlockedEpisode::route('/create'),
            'edit' => Pages\EditUnlockedEpisode::route('/{record}/edit'),
        ];
    }
}
