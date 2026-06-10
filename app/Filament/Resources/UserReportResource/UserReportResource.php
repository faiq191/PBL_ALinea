<?php

namespace App\Filament\Resources\UserReportResource;

use App\Filament\Resources\UserReportResource\Pages\CreateUserReport;
use App\Filament\Resources\UserReportResource\Pages\EditUserReport;
use App\Filament\Resources\UserReportResource\Pages\ListUserReports;
use App\Filament\Resources\UserReportResource\Schemas\UserReportForm;
use App\Filament\Resources\UserReportResource\Tables\UserReportsTable;
use App\Models\UserReport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class UserReportResource extends Resource
{
    protected static ?string $model = UserReport::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Laporan Pengguna';

    protected static ?string $modelLabel = 'Laporan Pengguna';

    protected static ?string $pluralModelLabel = 'Laporan Pengguna';

    protected static ?int $navigationSort = 6;

    public static function form(Schema $schema): Schema
    {
        return UserReportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserReportsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserReports::route('/'),
            'create' => CreateUserReport::route('/create'),
            'edit' => EditUserReport::route('/{record}/edit'),
        ];
    }
}
