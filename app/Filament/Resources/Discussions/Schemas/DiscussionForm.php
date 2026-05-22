<?php

namespace App\Filament\Resources\Discussions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DiscussionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('genre'),
                TextInput::make('category'),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
            ]);
    }
}
