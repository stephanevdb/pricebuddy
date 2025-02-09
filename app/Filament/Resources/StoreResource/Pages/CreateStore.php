<?php

namespace App\Filament\Resources\StoreResource\Pages;

use App\Filament\Resources\StoreResource;
use App\Filament\Resources\StoreResource\Pages\Traits\TestAfterEdit;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\CreateRecord;

class CreateStore extends CreateRecord
{
    use TestAfterEdit;

    protected static string $resource = StoreResource::class;

    /**
     * @return array<Action | ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction(),
            $this->getSaveAndTestAction(__('Create & test')),
        ];
    }

    public function saveAndTest(): void
    {
        $this->testAfterCreate = true;
        $this->create();
    }
}
