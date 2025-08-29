<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CoinTopUpResource\Pages;
use App\Filament\Resources\CoinTopUpResource\RelationManagers;
use App\Models\CoinPackage;
use App\Models\CoinTopUp;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Illuminate\Support\Str;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CoinTopUpResource extends Resource
{
    protected static ?string $model = CoinTopUp::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationLabel = 'Top Up Koin';
    protected static ?string $pluralModelLabel = 'Top Up Koin';

    protected static ?string $navigationGroup = 'Koin';

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->default('TOPUP-' . Str::random(10))
                    ->readOnly(),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('coin_package_id')
                    ->label('Paket Koin')
                    ->options(CoinPackage::all()->pluck('title', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ])
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User'),
                Tables\Columns\TextColumn::make('package.title')
                    ->label('Paket Koin'),
                Tables\Columns\TextColumn::make('coin_amount')
                    ->label('Jumlah Koin'),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->label('Total Harga'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal TopUp')
                    ->dateTime('d-m-y H:i:s')
                    ->sortable(),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->options(User::all()->pluck('name', 'id'))
                    ->searchable(),
                Tables\Filters\SelectFilter::make('coin_package_id')
                    ->label('Paket Koin')
                    ->options(CoinPackage::all()->pluck('title', 'id'))
                    ->searchable(),
            ])

            ->actions([])
            ->bulkActions([]);
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
            'index' => Pages\ListCoinTopUps::route('/'),
            'create' => Pages\CreateCoinTopUp::route('/create'),
            'edit' => Pages\EditCoinTopUp::route('/{record}/edit'),
        ];
    }
}
