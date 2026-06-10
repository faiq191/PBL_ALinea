<?php

namespace App\Filament\Resources\BookReportResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use App\Models\CustomNotification;

class BookReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reporter.name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('book.title')
                    ->label('Buku Terlapor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reason')
                    ->label('Alasan Pelaporan')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'danger',
                        'resolved' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Tertunda',
                        'resolved' => 'Selesai',
                        default => $state,
                    }),
                TextColumn::make('created_at')
                    ->label('Tanggal Laporan')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->actions([
                // Aksi Beri Peringatan Kustom ke Pemilik Buku (Bahasa Indonesia sesuai KBBI)
                Action::make('give_warning')
                    ->label('Beri Peringatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->form([
                        Select::make('preset_warning')
                            ->label('Pilih Peringatan Standar')
                            ->options([
                                'custom' => 'Tulis Peringatan Kustom...',
                                'hak_cipta' => 'Pelanggaran Hak Cipta / Konten Duplikat',
                                'konten' => 'Buku mengandung konten tidak pantas',
                                'spam' => 'Katalog buku terindikasi spam / tidak valid',
                                'pedoman' => 'Pelanggaran pedoman komunitas Alinea',
                            ])
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => match ($state) {
                                'hak_cipta' => $set('warning_message', 'Buku yang Anda bagikan dilaporkan karena terindikasi melanggar hak cipta atau memiliki konten duplikat. Harap unggah karya orisinal atau buku yang sah di komunitas Alinea.'),
                                'konten' => $set('warning_message', 'Buku yang Anda bagikan dilaporkan karena mengandung deskripsi atau metadata tidak pantas. Buku tersebut telah ditinjau dan disesuaikan oleh administrator.'),
                                'spam' => $set('warning_message', 'Buku yang Anda bagikan dilaporkan karena memiliki data tidak valid, spam katalog, atau informasi palsu.'),
                                'pedoman' => $set('warning_message', 'Buku yang Anda bagikan dilaporkan karena melanggar pedoman komunitas Alinea. Pelanggaran berulang dapat mengakibatkan penangguhan akun.'),
                                default => $set('warning_message', ''),
                            }),
                        Textarea::make('warning_message')
                            ->label('Pesan Peringatan (Teks Notifikasi)')
                            ->required()
                            ->rows(4)
                            ->placeholder('Tulis pesan peringatan yang akan dikirim ke pemilik buku terlapor...'),
                    ])
                    ->action(function ($record, array $data) {
                        $book = $record->book;
                        if ($book && $book->user_id) {
                            // Kirim notifikasi peringatan ke pemilik buku terlapor
                            CustomNotification::send(
                                $book->user_id,
                                'Peringatan tentang Buku Anda',
                                $data['warning_message'],
                                '/books/' . $book->id,
                                auth()->id()
                            );
                        }

                        // Ubah status laporan menjadi selesai (resolved)
                        $record->update(['status' => 'resolved']);

                        // Tampilkan notifikasi toast di admin panel
                        Notification::make()
                            ->title('Peringatan Berhasil Dikirim')
                            ->body('Peringatan telah dikirim ke pemilik buku terlapor dan status laporan diselesaikan.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending' && $record->book && $record->book->user_id),

                // Aksi Tandai Selesai Cepat
                Action::make('resolve')
                    ->label('Tandai Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function ($record) {
                        $record->update(['status' => 'resolved']);
                        Notification::make()
                            ->title('Laporan Diselesaikan')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
