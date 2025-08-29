<?php

namespace App\Filament\Resources\CoinTopUpResource\Pages;

use App\Filament\Resources\CoinTopUpResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCoinTopUps extends ListRecords
{
    protected static string $resource = CoinTopUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('TopUp Coin'),
        ];
    }
}
