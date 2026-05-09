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

        return back()->with('success', 'Buku telah dikembalikan.');
    }

    public function myLoans()
    {
        $loans = Loan::where('borrower_id', auth()->id())
            ->with('book')
            ->latest()
            ->get();

        return view('loans.my_loans', compact('loans'));
    }

    public function incomingRequests()
    {
        $loans = Loan::where('owner_id', auth()->id())
            ->with(['book', 'borrower'])
            ->latest()
            ->get();

        return view('loans.incoming', compact('loans'));
    }
}