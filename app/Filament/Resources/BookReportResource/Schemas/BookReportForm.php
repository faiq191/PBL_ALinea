<?php

namespace App\Filament\Resources\BookReportResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;

class BookReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reporter_id')
                    ->label('Pelapor')
                    ->formatStateUsing(fn ($record) => $record?->reporter?->name)
                    ->disabled(),
                Textarea::make('reason')
                    ->label('Alasan Pelaporan')
                    ->disabled()
                    ->rows(3),
                Select::make('status')
                    ->label('Status Laporan')
                    ->options([
                        'pending' => 'Tertunda',
                        'resolved' => 'Selesai',
                    ])
                    ->required(),
                
                // Bukti Lampiran Buku
                Placeholder::make('bukti_buku')
                    ->label('Buku yang Dilaporkan')
                    ->content(function ($record) {
                        $book = $record?->book;
                        if (!$book) return 'Buku tidak ditemukan.';
                        
                        $html = '<div class="space-y-3 p-4 bg-gray-50 border border-gray-200 rounded-2xl flex gap-4">';
                        
                        if ($book->image) {
                            $src = str_starts_with($book->image, 'http') ? $book->image : asset('storage/' . $book->image);
                            $html .= '<img src="' . $src . '" class="w-20 h-28 rounded-xl shadow-sm border border-gray-200 object-cover shrink-0" />';
                        }
                        
                        $html .= '<div class="flex-1 min-w-0">';
                        $html .= '<p class="text-sm font-bold text-[#1a3a5c] mb-1">' . e($book->title) . '</p>';
                        $html .= '<p class="text-xs text-gray-500 font-semibold mb-2">Oleh: ' . e($book->author ?? '-') . '</p>';
                        $html .= '<p class="text-xs text-gray-700 font-semibold mb-2">Pemilik Koleksi: ' . e($book->user->name ?? 'Unknown') . '</p>';
                        $html .= '<p class="text-xs text-gray-500 line-clamp-3 leading-relaxed">' . e($book->description ?? 'Tidak ada sinopsis.') . '</p>';
                        $html .= '</div>';
                        
                        $html .= '</div>';
                        return new HtmlString($html);
                    }),
            ]);
    }
}
