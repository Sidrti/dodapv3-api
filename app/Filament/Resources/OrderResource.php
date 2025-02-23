<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Orders';
    protected static ?string $pluralLabel = 'Orders';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('first_name')->required(),
                Forms\Components\TextInput::make('last_name')->required(),
                Forms\Components\TextInput::make('email')->email()->required(),
                Forms\Components\TextInput::make('mobile_number')->required(),
                Forms\Components\Textarea::make('street_address')->required(),
                Forms\Components\Textarea::make('unit_address')->nullable(),
                Forms\Components\TextInput::make('total_amount')->numeric()->required(),
                Forms\Components\TextInput::make('fixed_charge')->numeric()->default(0),
                Forms\Components\TextInput::make('payment_status')->required(),
                Forms\Components\TextInput::make('transaction_id')->nullable(),
                Forms\Components\DateTimePicker::make('appointment_date')->required(),
                Forms\Components\TextInput::make('time_slot')->required(),
                Forms\Components\Select::make('service_id')->relationship('service', 'name')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('first_name')->sortable(),
                TextColumn::make('last_name')->sortable(),
                TextColumn::make('email'),
                TextColumn::make('mobile_number'),
                TextColumn::make('street_address'),
                TextColumn::make('total_amount')->sortable(),
                TextColumn::make('payment_status'),
                TextColumn::make('transaction_id'),
                TextColumn::make('appointment_date'),
                TextColumn::make('time_slot'),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListOrders::route('/'),
            // 'view' => Pages\ListOrders::route('/{record}'),
            // 'create' => Pages\CreateOrder::route('/create'),
            // 'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
