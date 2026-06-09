<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function store($bookId)
    {
        $book = Book::findOrFail($bookId);

        if ($book->user_id === auth()->id()) {
            return back()->with('error', 'Tidak bisa meminjam buku sendiri.');
        }

        if (!$book->isAvailable()) {
            return back()->with('error', 'Buku sedang tidak tersedia.');
        }

        $exists = Loan::where('book_id', $bookId)
            ->where('borrower_id', auth()->id())
            ->whereIn('status', ['pending', 'dipinjam'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Kamu sudah mengajukan peminjaman buku ini.');
        }

        Loan::create([
            'book_id'     => $bookId,
            'borrower_id' => auth()->id(),
            'owner_id'    => $book->user_id,
            'status'      => 'pending',
        ]);

        \App\Models\CustomNotification::send(
            $book->user_id,
            'Permintaan Peminjaman Baru',
            auth()->user()->name . ' ingin meminjam buku Anda: "' . $book->title . '".',
            '/loans/incoming',
            auth()->id()
        );

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back()->with('success', 'Permintaan peminjaman dikirim.');
    }

    public function updateStatus(Request $request, $loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->owner_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'status'      => $request->status,
            'borrowed_at' => $request->status === 'dipinjam' ? now() : $loan->borrowed_at,
            'returned_at' => $request->status === 'dikembalikan' ? now() : null,
        ]);

        $statusText = $request->status === 'dipinjam' ? 'disetujui' : ($request->status === 'dikembalikan' ? 'dikonfirmasi telah dikembalikan' : $request->status);
        \App\Models\CustomNotification::send(
            $loan->borrower_id,
            'Update Status Peminjaman',
            'Permintaan peminjaman buku "' . $loan->book->title . '" Anda telah ' . $statusText . '.',
            '/loans/my',
            auth()->id()
        );

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back()->with('success', 'Status diperbarui.');
    }

    // FUNGSI BARU: Untuk mengembalikan buku
    public function returnBook($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->borrower_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'status'      => 'dikembalikan',
            'returned_at' => now(),
        ]);

        \App\Models\CustomNotification::send(
            $loan->owner_id,
            'Buku Telah Dikembalikan oleh Peminjam',
            $loan->borrower->name . ' telah mengembalikan buku "' . $loan->book->title . '" Anda. Silakan verifikasi dan ubah status jika sudah diterima.',
            '/loans/incoming',
            auth()->id()
        );

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back()->with('success', 'Buku telah dikembalikan.');
    }

    public function myLoans()
    {
        return redirect('/koleksi');
    }

    public function incomingRequests()
    {
        return redirect('/koleksi');
    }

    // FUNGSI BARU: Untuk menagih pengembalian buku
    public function remindUser($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->owner_id !== auth()->id()) {
            abort(403);
        }

        \App\Models\CustomNotification::send(
            $loan->borrower_id,
            'Tagihan Pengembalian Buku',
            'Pemilik buku "' . $loan->book->title . '" meminta Anda untuk segera mengembalikan buku tersebut.',
            '/koleksi',
            auth()->id()
        );

        return back()->with('success', 'Tagihan pengembalian buku telah dikirim.');
    }
}