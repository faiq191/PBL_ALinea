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
                // Aksi Edit Buku
                Action::make('edit_book')
                    ->label('Edit Buku')
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary')
                    ->url(fn ($record) => $record->book ? route('filament.admin.resources.books.edit', ['record' => $record->book]) : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->book !== null),

                // Aksi Hapus Buku
                Action::make('delete_book')
                    ->label('Hapus Buku')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Hapus Buku Terlapor')
                    ->modalDescription('Apakah Anda yakin ingin menghapus buku ini? Tindakan ini tidak dapat dibatalkan.')
                    ->action(function ($record) {
                        $book = $record->book;
                        if ($book) {
                            $book->delete();
                        }
                        $record->update(['status' => 'resolved']);
                        Notification::make()
                            ->title('Buku Berhasil Dihapus')
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status === 'pending' && $record->book !== null),

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
