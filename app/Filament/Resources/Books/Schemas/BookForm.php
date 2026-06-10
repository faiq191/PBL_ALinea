<?php

namespace App\Filament\Resources\Books\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class BookForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Buku')
                    ->required()
                    ->maxLength(255),
                TextInput::make('author')
                    ->label('Nama Pengarang')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('image')
                    ->label('Sampul Buku')
                    ->image()
                    ->directory('books'),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Pemilik / Pengunggah')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('type_id')
                    ->relationship('type', 'name')
                    ->label('Tipe Buku')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('year_id')
                    ->relationship('year', 'year')
                    ->label('Tahun Terbit')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('demographic_id')
                    ->relationship('demographic', 'name')
                    ->label('Demografi Pembaca')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('genres')
                    ->relationship('genres', 'name')
                    ->multiple()
                    ->label('Genre Buku')
                    ->preload(),
                Textarea::make('description')
                    ->label('Sinopsis / Deskripsi Buku')
                    ->columnSpanFull()
                    ->rows(6),
            ]);
    }
}
