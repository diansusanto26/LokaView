<?php

namespace App\Filament\Resources\UnlockedEpisodeResource\Pages;

use App\Filament\Resources\UnlockedEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnlockedEpisodes extends ListRecords
{
    protected static string $resource = UnlockedEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
