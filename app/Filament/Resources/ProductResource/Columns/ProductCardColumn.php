<?php

namespace App\Filament\Resources\ProductResource\Columns;

use Filament\Tables\Columns\Column;
use Illuminate\Contracts\View\View;

class ProductCardColumn extends Column
{
    protected string $view = 'components.product-card-detail';

    public function render(): View
    {
        $this->viewData([
            'product' => $this->getRecord(),
            'standalone' => true,
        ]);

        return parent::render();
    }
}
