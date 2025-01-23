<?php

namespace Modules\Base\Filament\Resources;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Miguilim\FilamentAutoPanel\AutoResource;


class BaseResource extends AutoResource
{

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 1;


}
