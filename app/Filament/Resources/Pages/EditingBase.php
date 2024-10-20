<?php

namespace Modules\Base\Filament\Resources\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditingBase extends EditRecord
{
    protected static string $resource = '';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

}
