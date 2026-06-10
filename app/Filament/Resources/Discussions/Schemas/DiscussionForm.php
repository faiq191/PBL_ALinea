<?php

namespace App\Filament\Resources\Discussions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Schema;

class DiscussionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Diskusi')
                    ->required()
                    ->maxLength(255),
                TextInput::make('genre')
                    ->label('Genre')
                    ->maxLength(255),
                TextInput::make('category')
                    ->label('Kategori')
                    ->maxLength(255),
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Pembuat Diskusi')
                    ->searchable()
                    ->preload()
                    ->required(),
                FileUpload::make('image')
                    ->label('Gambar / Media Lampiran')
                    ->image()
                    ->directory('discussions')
                    ->columnSpanFull(),
                Textarea::make('content')
                    ->label('Konten Diskusi')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }
}
