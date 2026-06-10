<?php

namespace App\Filament\Resources\UserReportResource\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Illuminate\Support\HtmlString;
use Filament\Schemas\Schema;

class UserReportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('reporter_id')
                    ->label('Pelapor')
                    ->formatStateUsing(fn ($record) => $record?->reporter?->name)
                    ->disabled(),
                TextInput::make('reported_id')
                    ->label('Dilaporkan')
                    ->formatStateUsing(fn ($record) => $record?->reported?->name)
                    ->disabled(),
                TextInput::make('reported_type')
                    ->label('Tipe Konten')
                    ->disabled(),
                Textarea::make('reason')
                    ->label('Alasan Pelaporan')
                    ->disabled()
                    ->rows(3),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Tertunda',
                        'resolved' => 'Selesai',
                    ])
                    ->required(),
                
                // Bukti Lampiran jika tersedia
                Placeholder::make('bukti_diskusi')
                    ->label('Bukti Diskusi')
                    ->content(function ($record) {
                        $discussion = $record?->discussion;
                        if (!$discussion) return 'Diskusi tidak ditemukan.';
                        
                        $html = '<div class="space-y-3 p-4 bg-gray-50 border border-gray-200 rounded-2xl flex gap-4">';
                        
                        if ($discussion->image) {
                            $src = str_starts_with($discussion->image, 'http') ? $discussion->image : asset('storage/' . $discussion->image);
                            $html .= '<img src="' . $src . '" class="w-20 h-28 rounded-xl shadow-sm border border-gray-200 object-cover shrink-0" />';
                        }
                        
                        $html .= '<div class="flex-1 min-w-0">';
                        $html .= '<p class="text-sm font-bold text-[#1a3a5c] mb-1">' . e($discussion->title) . '</p>';
                        $html .= '<span class="inline-block text-[10px] font-bold text-white bg-[#1a3a5c] px-2 py-0.5 rounded-full uppercase tracking-wider mb-2">' . e($discussion->genre ?? 'Umum') . '</span>';
                        $html .= '<p class="text-xs text-gray-600 whitespace-pre-wrap leading-relaxed line-clamp-4">' . e($discussion->content) . '</p>';
                        $html .= '</div>';
                        
                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->visible(fn ($record) => $record && $record->reported_type === 'discussion'),
                
                Placeholder::make('bukti_komentar')
                    ->label('Bukti Komentar/Balasan')
                    ->content(function ($record) {
                        $comment = $record?->comment;
                        if (!$comment) return 'Komentar tidak ditemukan.';
                        
                        $html = '<div class="space-y-3 p-4 bg-gray-50 border border-gray-200 rounded-2xl">';
                        
                        // Text content
                        if ($comment->content) {
                            $html .= '<p class="text-sm font-medium text-gray-800 whitespace-pre-wrap">' . e($comment->content) . '</p>';
                        } else {
                            $html .= '<p class="text-sm text-gray-400 italic">Tidak ada teks komentar</p>';
                        }

                        // Attachment preview
                        if ($comment->attachment_path) {
                            $html .= '<div class="mt-2 pt-2 border-t border-gray-200">';
                            $html .= '<p class="text-xs font-bold text-gray-500 mb-1">Lampiran (' . strtoupper($comment->attachment_type) . '):</p>';
                            
                            if ($comment->attachment_type === 'image' || $comment->attachment_type === 'giphy' || $comment->attachment_type === 'tenor') {
                                $src = str_starts_with($comment->attachment_path, 'http') ? $comment->attachment_path : asset('storage/' . $comment->attachment_path);
                                $html .= '<img src="' . $src . '" class="max-h-48 rounded-xl shadow-sm border border-gray-200 object-contain" />';
                            } elseif ($comment->attachment_type === 'gmaps') {
                                $html .= '<div class="flex items-center gap-1 text-xs text-blue-600 font-semibold">';
                                $html .= '<span class="px-2 py-1 bg-blue-50 rounded-lg border border-blue-100 flex items-center gap-1"><svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg> Lokasi: ' . e($comment->attachment_name ?? 'Koordinat ' . $comment->attachment_path) . '</span>';
                                $html .= '</div>';
                            } else {
                                $src = asset('storage/' . $comment->attachment_path);
                                $html .= '<a href="' . $src . '" download class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-gray-200 rounded-lg text-xs font-semibold text-gray-700 hover:bg-gray-50">';
                                $html .= '<svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>';
                                $html .= e($comment->attachment_name ?? 'Unduh File');
                                $html .= '</a>';
                            }
                            $html .= '</div>';
                        }
                        
                        $html .= '</div>';
                        return new HtmlString($html);
                    })
                    ->visible(fn ($record) => $record && $record->reported_type === 'comment'),
            ]);
    }
}
