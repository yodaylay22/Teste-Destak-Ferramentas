<?php

namespace App\Filament\Widgets;

use App\Models\MlCategory;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{

    protected ?string $heading = 'Estatísticas';

    protected function getStats(): array
    {
        return [
            Stat::make('Produtos cadastrados', Product::count())->description('Produtos cadastrados pelo usuário')->descriptionIcon('heroicon-o-cube'),
            Stat::make('Produtos enviados', Product::whereNotNull('ml_id')->count())->description('Produtos enviados para o Mercado Livre')->color('success')->descriptionIcon('heroicon-o-arrow-up-right'),
            Stat::make('Categorias', MlCategory::count())->description('Total de categorias registradas')->color('info')->descriptionIcon('heroicon-o-tag'),
        ];
    }
}
