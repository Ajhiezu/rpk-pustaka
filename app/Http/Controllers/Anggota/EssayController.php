<?php

namespace App\Http\Controllers\Anggota;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Essay;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EssayController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. User's own essays with review statuses
        $myEssays = Essay::where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        // 2. Published essays from all members
        $publishedEssays = Essay::with('user')
            ->published()
            ->latest()
            ->limit(6)
            ->get();

        return view('member.essays.index', compact('myEssays', 'publishedEssays'));
    }

    public function create()
    {
        return view('member.essays.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'submission_type' => 'required|in:online,file',
            'content' => 'required_if:submission_type,online|nullable|string',
            'essay_file' => 'required_if:submission_type,file|nullable|file|mimes:pdf,docx,doc|max:10240',
        ]);

        $data = [
            'user_id' => Auth::id(),
            'title' => $request->title,
            'submission_type' => $request->submission_type,
            'status' => 'submitted',
        ];

        if ($request->submission_type === 'online') {
            $data['content'] = $request->content;
        } else {
            $file = $request->file('essay_file');
            $extension = strtolower($file->getClientOriginalExtension());
            $filePath = $file->store('essays', 'local'); // Strictly private storage

            $data['file_path'] = $filePath;
            $data['file_type'] = $extension;
        }

        Essay::create($data);

        return redirect()->route('anggota.essays.index')
            ->with('success', 'Tulisan berhasil diajukan! Dewan kurator perpustakaan akan segera meninjaunya.');
    }

    public function show(Essay $essay)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan masuk ke akun anggota/admin Anda terlebih dahulu untuk membaca esai.');
        }

        // IDOR Protection: only the author or admin can view unpublished essays
        if ($essay->user_id !== $user->id && !$essay->isPublished() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak: Anda tidak memiliki izin untuk melihat tulisan ini.');
        }

        return view('member.essays.show', compact('essay'));
    }

    public function downloadFile(Essay $essay)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // IDOR Protection on private file download
        if ($essay->user_id !== $user->id && !$essay->isPublished() && !$user->isAdmin()) {
            abort(403, 'Akses ditolak.');
        }

        if (!$essay->file_path || !Storage::disk('local')->exists($essay->file_path)) {
            abort(404, 'Berkas tulisan tidak ditemukan.');
        }

        $disk = Storage::disk('local');
        $mime = $essay->file_type === 'pdf' ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';

        return response()->download($disk->path($essay->file_path), basename($essay->title) . '.' . $essay->file_type, [
            'Content-Type' => $mime,
        ]);
    }
}
