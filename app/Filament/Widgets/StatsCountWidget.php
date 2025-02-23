<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsCountWidget extends BaseWidget
{
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getCards(): array
    {
        return [
            Card::make('Total Users', User::count() - 1)
                ->description('Total number of registered users')
                ->descriptionIcon('heroicon-o-users')
                ->color('error') // Primary color for users
                ->icon('heroicon-o-user'),

            Card::make('Total Orders', Order::count())
                ->description('Total number of service requested')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('primary') // Blue color for orders
                ->icon('heroicon-o-shopping-cart'),

            Card::make('Total Services', Service::count())
                ->description('Total number of services offered')
                ->descriptionIcon('heroicon-o-collection')
                ->color('information') // Yellow color for services
                ->icon('heroicon-o-collection'),

            Card::make('Total Revenue', Order::where('payment_status', 'SUCCESS')->sum('total_amount'))
                ->description('Total Revenue generated from completed orders')
                ->descriptionIcon('heroicon-o-cash')
                ->color('success') // Green color for revenue
                ->icon('heroicon-o-cash')
        ];
    }
}
