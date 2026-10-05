<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Essay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EssayController extends Controller
{
    public function index(Request $request)
    {
        $query = Essay::with(['user', 'reviewer']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$search}%"));
            });
        }

        $essays = $query->latest()->paginate(10)->withQueryString();

        return view('admin.essays.index', compact('essays'));
    }

    public function show(Essay $essay)
    {
        $essay->load(['user', 'reviewer']);
        return view('admin.essays.show', compact('essay'));
    }

    public function updateStatus(Request $request, Essay $essay)
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,approved,rejected,revision,published',
            'review_note' => 'nullable|string',
        ]);

        $essay->update([
            'status' => $validated['status'],
            'review_note' => $validated['review_note'] ?? null,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        $statusLabels = [
            'approved' => 'disetujui',
            'rejected' => 'ditolak',
            'revision' => 'diminta revisi',
            'published' => 'dipublikasikan',
            'submitted' => 'dikembalikan ke status pengajuan',
        ];

        $statusMsg = $statusLabels[$validated['status']] ?? $validated['status'];

        return redirect()->route('admin.essays.index')
            ->with('success', "Status esai '{$essay->title}' berhasil diubah menjadi {$statusMsg}.");
    }

    public function downloadFile(Essay $essay)
    {
        if (!$essay->file_path || !Storage::disk('local')->exists($essay->file_path)) {
            abort(404, 'Berkas tulisan tidak ditemukan di server.');
        }

        $disk = Storage::disk('local');
        $mime = $essay->file_type === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        return response()->download($disk->path($essay->file_path), basename($essay->title) . '.' . $essay->file_type, [
            'Content-Type' => $mime,
        ]);
    }
}
