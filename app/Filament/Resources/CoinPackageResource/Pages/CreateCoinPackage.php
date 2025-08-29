<?php

namespace App\Filament\Resources\CoinPackageResource\Pages;

use App\Filament\Resources\CoinPackageResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCoinPackage extends CreateRecord
{
    protected static string $resource = CoinPackageResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Harga Koin Berhasil Ditambahkan';
    }
}
