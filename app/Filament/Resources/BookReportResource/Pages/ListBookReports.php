<?php

namespace App\Filament\Resources\BookReportResource\Pages;

use App\Filament\Resources\BookReportResource\BookReportResource;
use Filament\Resources\Pages\ListRecords;

class ListBookReports extends ListRecords
{
    protected static string $resource = BookReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
