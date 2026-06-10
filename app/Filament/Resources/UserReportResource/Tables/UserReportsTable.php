<?php

namespace App\Filament\Resources\UserReportResource\Tables;

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

class UserReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reporter.name')
                    ->label('Pelapor')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reported.name')
                    ->label('Dilaporkan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reported_type')
                    ->label('Tipe Konten')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discussion' => 'info',
                        'comment' => 'warning',
                        'profile' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'discussion' => 'Diskusi',
                        'comment' => 'Komentar',
                        'profile' => 'Profil',
                        default => $state,
                    }),
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
                // Aksi Beri Peringatan Kustom (Bahasa Indonesia sesuai KBBI)
                Action::make('give_warning')
                    ->label('Beri Peringatan')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('warning')
                    ->form([
                        Select::make('preset_warning')
                            ->label('Pilih Peringatan Standar')
                            ->options([
                                'custom' => 'Tulis Peringatan Kustom...',
                                'sopan' => 'Perilaku tidak sopan atau kasar di komunitas',
                                'konten' => 'Mengunggah konten atau media tidak pantas',
                                'spam' => 'Penyebaran spam atau iklan ilegal',
                                'pedoman' => 'Pelanggaran pedoman komunitas Alinea',
                            ])
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => match ($state) {
                                'sopan' => $set('warning_message', 'Akun Anda telah dilaporkan karena berperilaku tidak sopan atau menggunakan kata-kata kasar di komunitas Alinea. Harap menjaga kesopanan dalam berinteraksi sesama pengguna.'),
                                'konten' => $set('warning_message', 'Akun Anda dilaporkan karena mengunggah konten atau media yang dinilai tidak pantas. Konten tersebut kini telah dihapus oleh moderator. Harap patuhi aturan publikasi kami.'),
                                'spam' => $set('warning_message', 'Akun Anda dilaporkan karena menyebarkan spam, pesan berulang, atau iklan komersial tanpa izin di platform kami.'),
                                'pedoman' => $set('warning_message', 'Akun Anda dilaporkan karena melanggar ketentuan dan pedoman komunitas Alinea. Pelanggaran lebih lanjut dapat mengakibatkan penangguhan akun.'),
                                default => $set('warning_message', ''),
                            }),
                        Textarea::make('warning_message')
                            ->label('Pesan Peringatan (Teks Notifikasi)')
                            ->required()
                            ->rows(4)
                            ->placeholder('Tulis pesan peringatan yang akan dikirim ke pengguna terlapor...'),
                    ])
                    ->action(function ($record, array $data) {
                        // Kirim notifikasi peringatan ke pengguna terlapor
                        CustomNotification::send(
                            $record->reported_id,
                            'Peringatan dari Administrator',
                            $data['warning_message'],
                            null,
                            auth()->id()
                        );

                        // Ubah status laporan menjadi selesai (resolved)
                        $record->update(['status' => 'resolved']);

                        // Tampilkan notifikasi toast di admin panel
                        Notification::make()
                            ->title('Peringatan Berhasil Dikirim')
                            ->body('Notifikasi peringatan telah dikirim ke pengguna terlapor dan status laporan diselesaikan.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending'),

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
