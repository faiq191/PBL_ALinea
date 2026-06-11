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
                    ->label('Judul Acara')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Deskripsi / Penjelasan Acara')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Poster / Pamflet Acara')
                    ->image()
                    ->directory('events')
                    ->disk('public')
                    ->columnSpanFull(),
                TextInput::make('link')
                    ->label('Tautan Pendaftaran / Informasi')
                    ->url()
                    ->maxLength(255)
                    ->placeholder('https://contoh.com/daftar-acara'),
                DateTimePicker::make('start_date')
                    ->label('Waktu Mulai')
                    ->required(),
                DateTimePicker::make('end_date')
                    ->label('Waktu Selesai')
                    ->required(),
                Select::make('status')
                    ->label('Status Kegiatan')
                    ->options([
                        'draft' => 'Draf / Rencana',
                        'running' => 'Sedang Berjalan',
                        'completed' => 'Selesai',
                    ])
                    ->default('running')
                    ->required(),
            ]);
    }
}
