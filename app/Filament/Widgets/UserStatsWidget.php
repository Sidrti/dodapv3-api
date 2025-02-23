<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class UserStatsWidget extends BaseWidget
{
    protected function getColumns(): int 
    {
        return 3;
    }
    protected function getCards(): array
    {
        return [
       
        ];
    }
}