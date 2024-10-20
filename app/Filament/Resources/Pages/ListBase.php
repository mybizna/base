<?php

namespace Modules\Base\Filament\Resources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBase extends ListRecords
{
    protected static string $resource = '';

    public static function setResource(string $resource)
    {
        static::$resource = $resource;

        return new self();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
