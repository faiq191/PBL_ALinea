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

    // FUNGSI BARU: Untuk mengembalikan buku (mengajukan permintaan kembali)
    public function returnBook($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->borrower_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'return_requested' => true,
        ]);

        \App\Models\CustomNotification::send(
            $loan->owner_id,
            'Permintaan Pengembalian Buku',
            $loan->borrower->name . ' ingin mengembalikan buku "' . $loan->book->title . '" Anda. Silakan konfirmasi jika sudah Anda terima.',
            '/koleksi',
            auth()->id()
        );

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back()->with('success', 'Permintaan pengembalian buku telah dikirim ke pemilik.');
    }

    // FUNGSI BARU: Pemilik mengonfirmasi pengembalian buku
    public function confirmReturn($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->owner_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'status'           => 'dikembalikan',
            'returned_at'      => now(),
            'return_requested' => false,
        ]);

        \App\Models\CustomNotification::send(
            $loan->borrower_id,
            'Pengembalian Buku Dikonfirmasi',
            'Pemilik buku telah mengonfirmasi pengembalian buku "' . $loan->book->title . '". Terima kasih!',
            '/koleksi',
            auth()->id()
        );

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back()->with('success', 'Pengembalian buku berhasil dikonfirmasi.');
    }

    // FUNGSI BARU: Pemilik menolak pengembalian buku (menyatakan belum menerima)
    public function rejectReturn($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->owner_id !== auth()->id()) {
            abort(403);
        }

        $loan->update([
            'return_requested' => false,
        ]);

        \App\Models\CustomNotification::send(
            $loan->borrower_id,
            'Pengembalian Buku Ditolak',
            'Pemilik menyatakan belum menerima buku "' . $loan->book->title . '". Silakan hubungi pemilik buku.',
            '/koleksi',
            auth()->id()
        );

        try { broadcast(new \App\Events\StatsUpdated()); } catch (\Exception $e) {}

        return back()->with('success', 'Permintaan pengembalian buku ditolak.');
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

        $cooldownKey = 'loan_remind_cooldown_' . $loan->id;
        if (\Illuminate\Support\Facades\Cache::has($cooldownKey)) {
            $expiresAt = \Illuminate\Support\Facades\Cache::get($cooldownKey);
            $remainingSeconds = max(0, $expiresAt - time());
            $minutes = ceil($remainingSeconds / 60);
            return back()->with('error', "Anda hanya dapat mengirim tagihan sekali setiap 5 menit. Silakan tunggu {$minutes} menit lagi.");
        }

        \App\Models\CustomNotification::send(
            $loan->borrower_id,
            'Tagihan Pengembalian Buku',
            'Pemilik buku "' . $loan->book->title . '" meminta Anda untuk segera mengembalikan buku tersebut.',
            '/koleksi',
            auth()->id()
        );

        // Cooldown 5 menit (300 detik)
        \Illuminate\Support\Facades\Cache::put($cooldownKey, time() + 300, 300);

        return back()->with('success', 'Tagihan pengembalian buku telah dikirim.');
    }
}