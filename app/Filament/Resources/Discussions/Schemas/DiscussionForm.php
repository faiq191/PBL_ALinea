<?php

namespace App\Filament\Resources\Discussions\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
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
                Section::make('Gambar / Media Lampiran')
                    ->description('Pilih salah satu: upload file gambar atau masukkan link URL gambar. Jika keduanya diisi, URL lebih diprioritaskan.')
                    ->components([
                        FileUpload::make('image_file')
                            ->label('Upload File')
                            ->image()
                            ->directory('discussions')
                            ->dehydrated(false),
                        TextInput::make('image_url')
                            ->label('Atau Link URL Gambar')
                            ->url()
                            ->placeholder('https://example.com/image.jpg'),
                    ]),
                Textarea::make('content')
                    ->label('Konten Diskusi')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
            ]);
    }
}
