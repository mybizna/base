<?php

namespace Modules\Base\Filament\Resources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListingBase extends ListRecords
{
    protected static string $resource = '';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

}
