<?php

namespace App\Filament\Resources\BookReportResource;

use App\Filament\Resources\BookReportResource\Pages\CreateBookReport;
use App\Filament\Resources\BookReportResource\Pages\EditBookReport;
use App\Filament\Resources\BookReportResource\Pages\ListBookReports;
use App\Filament\Resources\BookReportResource\Schemas\BookReportForm;
use App\Filament\Resources\BookReportResource\Tables\BookReportsTable;
use App\Models\BookReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BookReportResource extends Resource
{
    protected static ?string $model = BookReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Laporan Buku';

    protected static ?string $modelLabel = 'Laporan Buku';

    protected static ?string $pluralModelLabel = 'Laporan Buku';

    protected static string|\UnitEnum|null $navigationGroup = 'Laporan & Moderasi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return BookReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookReports::route('/'),
            'create' => CreateBookReport::route('/create'),
            'edit' => EditBookReport::route('/{record}/edit'),
        ];
    }
}
