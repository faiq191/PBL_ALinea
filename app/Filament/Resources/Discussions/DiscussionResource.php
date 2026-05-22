<?php

namespace App\Filament\Resources\Discussions;

use App\Filament\Resources\Discussions\Pages\CreateDiscussion;
use App\Filament\Resources\Discussions\Pages\EditDiscussion;
use App\Filament\Resources\Discussions\Pages\ListDiscussions;
use App\Filament\Resources\Discussions\Schemas\DiscussionForm;
use App\Filament\Resources\Discussions\Tables\DiscussionsTable;
use App\Models\Discussion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DiscussionResource extends Resource
{
    protected static ?string $model = Discussion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return DiscussionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DiscussionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDiscussions::route('/'),
            'create' => CreateDiscussion::route('/create'),
            'edit' => EditDiscussion::route('/{record}/edit'),
        ];
    }
}
