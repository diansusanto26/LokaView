<?php

namespace App\Filament\Resources\CoinTopUpResource\Pages;

use App\Filament\Resources\CoinTopUpResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCoinTopUp extends EditRecord
{
    protected static string $resource = CoinTopUpResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
