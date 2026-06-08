<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->directory('events')
                    ->columnSpanFull(),
                TextInput::make('link')
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://example.com/register-event'),
                DateTimePicker::make('start_date'),
                DateTimePicker::make('end_date'),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'running' => 'Running',
                        'completed' => 'Completed',
                    ])
                    ->default('running')
                    ->required(),
            ]);
    }
}
