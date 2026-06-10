<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserReport;
use App\Models\User;

class UserReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reported_id' => 'required|exists:users,id',
            'reported_type' => 'required|in:discussion,comment,profile',
            'reason' => 'required|string|max:1000',
            'discussion_id' => 'nullable|exists:discussions,id',
            'comment_id' => 'nullable|exists:comments,id',
        ], [
            'reported_id.required' => 'Pengguna terlapor wajib diisi.',
            'reported_id.exists' => 'Pengguna tidak ditemukan.',
            'reported_type.required' => 'Tipe pelaporan wajib diisi.',
            'reason.required' => 'Alasan pelaporan wajib diisi secara manual.',
            'reason.max' => 'Alasan pelaporan maksimal 1000 karakter.',
        ]);

        if (auth()->id() == $request->reported_id) {
            return back()->with('error', 'Anda tidak dapat melaporkan diri sendiri.');
        }

        UserReport::create([
            'reporter_id' => auth()->id(),
            'reported_id' => $request->reported_id,
            'reported_type' => $request->reported_type,
            'reason' => $request->reason,
            'discussion_id' => $request->discussion_id,
            'comment_id' => $request->comment_id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Laporan Anda berhasil dikirim dan akan segera diproses oleh admin.');
    }
}
