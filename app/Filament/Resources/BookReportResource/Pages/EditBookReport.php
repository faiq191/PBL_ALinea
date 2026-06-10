<?php

namespace App\Filament\Resources\BookReportResource\Pages;

use App\Filament\Resources\BookReportResource\BookReportResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBookReport extends EditRecord
{
    protected static string $resource = BookReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
