<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SeriesResource\Pages;
use App\Filament\Resources\SeriesResource\RelationManagers;
use App\Models\Genre;
use App\Models\Series;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SeriesResource extends Resource
{
    protected static ?string $model = Series::class;

    protected static ?string $navigationIcon = 'heroicon-o-tv';

    protected static ?string $navigationGroup = 'Menejemen Series';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('genre_id')
                    ->relationship('genre', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->label('Genre'),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->label('Judul'),
                Forms\Components\TextInput::make('slug')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)
                    ->label('Slug'),
                Forms\Components\RichEditor::make('description')
                    ->required()
                    ->label('Deskripsi'),
                Forms\Components\FileUpload::make('thumbnail')
                    ->required()
                    ->image()
                    ->label('Thumbnail'),
                Forms\Components\TextInput::make('age_rating')
                    ->required()
                    ->maxLength(255)
                    ->label('Rating Usia'),
                Forms\Components\TextInput::make('release_year')
                    ->label('Tahun Rilis')
                    ->maxValue((int) now()->year)
                    ->required(),
                Forms\Components\Toggle::make('is_trending')
                    ->required()
                    ->default(false)
                    ->label('Trending'),
                Forms\Components\Toggle::make('is_top_choice')
                    ->required()
                    ->default(false)
                    ->label('Top Choice'),
                Forms\Components\Repeater::make('episodes')
                    ->relationship('episodes')
                    ->schema([
                        Forms\Components\TextInput::make('episode_number')
                            ->required()
                            ->numeric()
                            ->label('Nomor Episode'),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->label('Judul'),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255)
                            ->label('Diskripsi'),
                        Forms\Components\FileUpload::make('video')
                            ->disk('public') // atau s3/minio
                            ->directory('videos')
                            ->acceptedFileTypes(['video/mp4', 'video/quicktime', 'video/x-matroska'])
                            ->maxSize(512000), // 500 MB
                        Forms\Components\Toggle::make('is_locked')
                            ->required()
                            ->default(false)
                            ->label('Terkunci'),
                        Forms\Components\TextInput::make('unlock_cost')
                            ->required()
                            ->numeric()
                            ->label('Koin Untuk Membuka Kunci'),
                    ])
                    ->label('Judul')
                    ->columnSpanFull()
                    ->grid(2)
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('genre.name')
                    ->searchable()
                    ->label('Genre'),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->label('Judul'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->label('Slug'),
                Tables\Columns\TextColumn::make('age_rating')
                    ->searchable()
                    ->label('Rating Usia'),
                Tables\Columns\TextColumn::make('release_year')
                    ->sortable()
                    ->label('Tahun Rilis'),
                Tables\Columns\ToggleColumn::make('is_trending')
                    ->label('Trending'),
                Tables\Columns\ToggleColumn::make('is_top_choice')
                    ->label('Top Choice'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Dibuat'),
                Tables\Columns\TextColumn::make('update_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Diperbarui'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('genre_id')
                    ->label('Genre')
                    ->options(Genre::all()->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
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
            'index' => Pages\ListSeries::route('/'),
            'create' => Pages\CreateSeries::route('/create'),
            'edit' => Pages\EditSeries::route('/{record}/edit'),
        ];
    }
}
