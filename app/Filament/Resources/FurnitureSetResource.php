<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FurnitureSetResource\Pages;
use App\Models\FurnitureSet;
use App\Services\ImageUploadService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FurnitureSetResource extends Resource
{
    protected static ?string $model = FurnitureSet::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Furniture Set Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),
                        Forms\Components\TextInput::make('price')
                            ->label('Regular Price (Strikethrough)')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\TextInput::make('sale_price')
                            ->label('Sale / Promo Price')
                            ->numeric()
                            ->prefix('$'),
                        Forms\Components\FileUpload::make('image_url')
                            ->label('Set Showcase Image')
                            ->image()
                            ->saveUploadedFileUsing(function ($file) {
                                return app(ImageUploadService::class)->store($file, 'furniture-sets');
                            }),
                        Forms\Components\Textarea::make('whatsapp_message')
                            ->label('Pre-filled WhatsApp Message')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('sort_order')
                            ->numeric()
                            ->default(0),
                        Forms\Components\Toggle::make('is_active')
                            ->default(true),
                    ])->columns(2),

                Forms\Components\Section::make('Included Sub-Products')
                    ->description('Assign products included in this furniture set and categorize them into groups (e.g. arm-chair, sofa, table).')
                    ->schema([
                        Forms\Components\Select::make('products')
                            ->relationship('products', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_url')
                    ->label('Image'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\TextColumn::make('sale_price')
                    ->money('USD')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFurnitureSets::route('/'),
            'create' => Pages\CreateFurnitureSet::route('/create'),
            'edit' => Pages\EditFurnitureSet::route('/{record}/edit'),
        ];
    }
}
