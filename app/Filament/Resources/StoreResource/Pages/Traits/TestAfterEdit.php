<?php

namespace App\Filament\Resources\StoreResource\Pages\Traits;

use Filament\Actions\Action;

trait TestAfterEdit
{
    protected bool $testAfterCreate = false;

    public function getSaveAndTestAction(string $label): Action
    {
        return Action::make('createAndTest')
            ->label($label)
            ->action('saveAndTest')
            ->keyBindings(['mod+shift+t'])
            ->color('gray');
    }

    protected function getRedirectUrl(): string
    {
        if ($this->testAfterCreate) {
            $this->testAfterCreate = false;

            return static::getResource()::getUrl('test', ['record' => $this->record->getKey()]);
        }

        return static::getResource()::getUrl('index');
    }
}
