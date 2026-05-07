<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    // User minta pinjam buku
    public function store($bookId)
    {
        $book = Book::findOrFail($bookId);

        // Tidak bisa pinjam buku sendiri
        if ($book->user_id === auth()->id()) {
            return back()->with('error', 'Tidak bisa meminjam buku sendiri.');
        }

        // Cek apakah buku sedang dipinjam
        if (!$book->isAvailable()) {
            return back()->with('error', 'Buku sedang tidak tersedia.');
        }

        // Cek apakah user sudah pernah request buku ini
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

        return back()->with('success', 'Permintaan peminjaman dikirim.');
    }

    // Pemilik buku approve/tolak
    public function updateStatus(Request $request, $loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->owner_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'status'      => $request->status, // 'dipinjam' atau 'dikembalikan'
            'borrowed_at' => $request->status === 'dipinjam' ? now() : $loan->borrowed_at,
            'returned_at' => $request->status === 'dikembalikan' ? now() : null,
        ]);

        return back()->with('success', 'Status diperbarui.');
    }

    // Halaman daftar peminjaman milik user (sebagai peminjam)
    public function myLoans()
    {
        $loans = Loan::where('borrower_id', auth()->id())
            ->with('book')
            ->latest()
            ->get();

        return view('loans.my_loans', compact('loans'));
    }

    // Halaman permintaan masuk (sebagai pemilik buku)
    public function incomingRequests()
    {
        $loans = Loan::where('owner_id', auth()->id())
            ->with(['book', 'borrower'])
            ->latest()
            ->get();

        return view('loans.incoming', compact('loans'));
    }
}
