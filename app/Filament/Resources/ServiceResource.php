<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Filament\Resources\ServiceResource\RelationManagers;
use App\Models\Service;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn; 
use Filament\Tables\Columns\ImageColumn; 
use Filament\Tables\Columns\ToggleColumn; 
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\RichEditor;


class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationLabel = 'Services';
    protected static ?string $pluralLabel = 'Services';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('name')->required()->maxLength(255),
            RichEditor::make('description')->required(),
            TextInput::make('price')->numeric()->required(),
            TextInput::make('discounted_price')->numeric()->nullable(),
            TextInput::make('fixed_charge')->numeric()->default(0),
            TextInput::make('duration')->required(),
            FileUpload::make('banner_image')->directory('services/banners')->nullable(),
            FileUpload::make('card_image')->directory('services/cards')->nullable(),
            TextInput::make('rating')->numeric()->default(0)->maxValue(5),
            TextInput::make('rater_count')->numeric()->default(0),
            Select::make('status')->options([
                'active' => 'Active',
                'inactive' => 'Inactive',
            ])->default('active'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            TextColumn::make('name')->sortable()->searchable(),
            TextColumn::make('price')->sortable(),
            TextColumn::make('discounted_price')->sortable(),
            TextColumn::make('duration'),
            ImageColumn::make('card_image'),
        ])
        ->filters([])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }    
}
