<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Actions\CreateAction;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('add_user')->resourceName('user'),
        ];
    }
}
