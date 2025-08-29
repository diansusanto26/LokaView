<?php

namespace App\Filament\Resources\UnlockedEpisodeResource\Pages;

use App\Filament\Resources\UnlockedEpisodeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnlockedEpisode extends EditRecord
{
    protected static string $resource = UnlockedEpisodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
