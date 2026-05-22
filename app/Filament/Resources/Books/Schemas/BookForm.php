<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('title'),
                TextInput::make('author'),
                FileUpload::make('image')
                    ->image(),
                TextInput::make('user_id')
                    ->numeric(),
                TextInput::make('genre'),
                TextInput::make('type_id')
                    ->numeric(),
                TextInput::make('year_id')
                    ->numeric(),
                TextInput::make('demographic_id')
                    ->numeric(),
            ]);
    }
}
