<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BookReport;
use App\Models\Book;

class BookReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'reason' => 'required|string|max:1000',
        ], [
            'book_id.required' => 'Buku yang dilaporkan wajib diisi.',
            'book_id.exists' => 'Buku tidak ditemukan.',
            'reason.required' => 'Alasan pelaporan wajib diisi secara manual.',
            'reason.max' => 'Alasan pelaporan maksimal 1000 karakter.',
        ]);

        $book = Book::findOrFail($request->book_id);

        BookReport::create([
            'reporter_id' => auth()->id(),
            'book_id' => $request->book_id,
            'reason' => $request->reason,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan buku berhasil dikirim dan akan segera ditinjau oleh admin.');
    }
}
