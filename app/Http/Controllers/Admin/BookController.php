<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $format = $request->input('format'); // all, physical, digital, hybrid
        
        $books = Book::with(['category', 'location'])
            ->when($search, function($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('title', 'LIKE', "%{$search}%")
                      ->orWhere('author', 'LIKE', "%{$search}%")
                      ->orWhere('isbn', 'LIKE', "%{$search}%")
                      ->orWhere('book_code', 'LIKE', "%{$search}%");
                });
            })
            ->when($format === 'digital', function($query) {
                $query->whereIn('collection_type', ['digital', 'fisik_digital']);
            })
            ->when($format === 'physical', function($query) {
                $query->whereIn('collection_type', ['fisik', 'fisik_digital']);
            })
            ->latest()
            ->paginate(10);
            
        return view('admin.books.index', compact('books'));
    }

    public function create()
    {
        $categories = Category::all();
        $locations = Location::all();
        $suggestedCode = $this->generateUniqueBookCode();
        return view('admin.books.create', compact('categories', 'locations', 'suggestedCode'));
    }

    public function store(Request $request)
    {
        $collectionType = $request->input('collection_type', 'fisik');

        // Retain previously uploaded temporary files if validation failed before
        $tempImage = $request->input('temp_image');
        $tempImageName = $request->input('temp_image_name');
        $tempPdf = $request->input('temp_pdf');
        $tempPdfName = $request->input('temp_pdf_name');

        // Immediately stash incoming valid files so they are never lost on validation errors
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $tempImage = $request->file('image')->store('temp/books/images', 'public');
            $tempImageName = $request->file('image')->getClientOriginalName();
        }

        if ($request->hasFile('pdf_file') && $request->file('pdf_file')->isValid()) {
            $tempPdf = $request->file('pdf_file')->store('temp/books/pdf', 'local');
            $tempPdfName = $request->file('pdf_file')->getClientOriginalName();
        }

        // Base validation rules
        $rules = [
            'book_code' => ['nullable', 'string', 'max:50', Rule::unique('books', 'book_code')->whereNull('deleted_at')],
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'collection_type' => 'required|in:fisik,digital,fisik_digital',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')->whereNull('deleted_at')],
            'language' => 'nullable|string|max:50',
            'page_count' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'fine_type' => 'nullable|in:fixed,multiplier,custom',
            'fine_value' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ];

        $hasRetainedPdf = !empty($tempPdf) && Storage::disk('local')->exists($tempPdf);

        // Conditional consistency rules
        if ($collectionType === 'digital') {
            $rules['pdf_file'] = $hasRetainedPdf ? 'nullable|file|mimes:pdf|max:51200' : 'required|file|mimes:pdf|max:51200';
            $rules['stock'] = 'nullable';
            $rules['location_id'] = 'nullable';
        } elseif ($collectionType === 'fisik') {
            $rules['stock'] = 'required|integer|min:1';
            $rules['location_id'] = 'required|exists:locations,id';
            $rules['pdf_file'] = 'nullable';
        } elseif ($collectionType === 'fisik_digital') {
            $rules['stock'] = 'required|integer|min:1';
            $rules['location_id'] = 'required|exists:locations,id';
            $rules['pdf_file'] = $hasRetainedPdf ? 'nullable|file|mimes:pdf|max:51200' : 'required|file|mimes:pdf|max:51200';
        }

        $messages = [
            'image.image' => 'Berkas sampul harus berupa file gambar.',
            'image.mimes' => 'Format berkas sampul hanya menerima PNG, JPG, JPEG, atau WEBP. Dokumen PDF tidak diperbolehkan untuk sampul.',
            'image.max' => 'Ukuran berkas sampul buku tidak boleh lebih dari 2MB.',
            'pdf_file.mimes' => 'Berkas naskah digital harus berformat dokumen PDF.',
            'pdf_file.max' => 'Ukuran berkas naskah PDF maksimal 50MB.',
            'pdf_file.required' => 'Berkas naskah PDF wajib diunggah untuk koleksi digital.',
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput($request->except(['image', 'pdf_file']) + [
                    'temp_image' => $tempImage,
                    'temp_image_name' => $tempImageName,
                    'temp_pdf' => $tempPdf,
                    'temp_pdf_name' => $tempPdfName,
                ]);
        }

        try {
            // Guarantee unique book_code
            if (empty($validated['book_code'])) {
                $validated['book_code'] = $this->generateUniqueBookCode();
            } else {
                // Free up any soft-deleted book that has the exact same book_code
                Book::withTrashed()->where('book_code', $validated['book_code'])->whereNotNull('deleted_at')->get()->each(function ($delBook) {
                    $delBook->book_code = $delBook->book_code . '_DEL_' . $delBook->id;
                    $delBook->saveQuietly();
                });
            }

            // If a soft-deleted book has the exact same ISBN, nullify its ISBN so DB unique constraint won't fail
            if (!empty($validated['isbn'])) {
                Book::withTrashed()->where('isbn', $validated['isbn'])->whereNotNull('deleted_at')->update(['isbn' => null]);
            }

            $validated['slug'] = Str::slug($request->title) . '-' . uniqid();
            $validated['language'] = $validated['language'] ?? 'Indonesia';
            $validated['price'] = $validated['price'] ?? 0;
            $validated['fine_type'] = $validated['fine_type'] ?? 'fixed';
            $validated['fine_value'] = $validated['fine_value'] ?? '50000';

            // Handle Collection Type Consistency
            if ($collectionType === 'digital') {
                $validated['stock'] = 0;
                $validated['available_stock'] = 0;
                $validated['location_id'] = null;
            } else {
                $validated['stock'] = (int) ($validated['stock'] ?? 1);
                $validated['available_stock'] = $validated['stock'];
            }

            // Move stashed or newly uploaded cover image to permanent storage
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                $path = $request->file('image')->store('books', 'public');
                $validated['image'] = $path;
            } elseif (!empty($tempImage) && Storage::disk('public')->exists($tempImage)) {
                $finalImagePath = 'books/' . basename($tempImage);
                Storage::disk('public')->move($tempImage, $finalImagePath);
                $validated['image'] = $finalImagePath;
            } elseif ($request->filled('auto_pdf_cover') && str_starts_with($request->input('auto_pdf_cover'), 'data:image')) {
                try {
                    $parts = explode(',', $request->input('auto_pdf_cover'), 2);
                    if (isset($parts[1])) {
                        $decoded = base64_decode($parts[1]);
                        if ($decoded !== false) {
                            $ext = str_contains($parts[0], 'image/webp') ? 'webp' : 'jpg';
                            $autoCoverPath = 'books/cover_auto_' . uniqid() . '.' . $ext;
                            Storage::disk('public')->put($autoCoverPath, $decoded);
                            $validated['image'] = $autoCoverPath;
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Auto cover save failed: ' . $e->getMessage());
                }
            }

            // Move stashed or newly uploaded PDF to permanent storage
            if (in_array($collectionType, ['digital', 'fisik_digital']) && !empty($tempPdf) && Storage::disk('local')->exists($tempPdf)) {
                $finalPdfPath = 'books/pdf/' . basename($tempPdf);
                Storage::disk('local')->move($tempPdf, $finalPdfPath);
                $validated['pdf_path'] = $finalPdfPath;
            }

            Book::create($validated);

            return redirect()->route('admin.books.index')->with('success', 'Koleksi naskah buku berhasil ditambahkan ke katalog!');
        } catch (\Exception $e) {
            Log::error('Book creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput($request->except(['image', 'pdf_file']) + [
                    'temp_image' => $tempImage,
                    'temp_image_name' => $tempImageName,
                    'temp_pdf' => $tempPdf,
                    'temp_pdf_name' => $tempPdfName,
                ])
                ->with('error', 'Gagal menambahkan buku: ' . $e->getMessage());
        }
    }

    public function show(Book $book)
    {
        $book->load(['category', 'location']);

        $recentLoans = $book->loanDetails()
            ->with(['loan.user', 'loan.returnBook'])
            ->latest()
            ->take(10)
            ->get();

        $activeReservations = $book->loanDetails()
            ->whereHas('loan', function ($q) {
                $q->whereIn('status', ['pending', 'approved', 'borrowed', 'overdue']);
            })
            ->with(['loan.user'])
            ->latest()
            ->get();

        return view('admin.books.show', compact('book', 'recentLoans', 'activeReservations'));
    }

    public function edit(Book $book)
    {
        $categories = Category::all();
        $locations = Location::all();
        return view('admin.books.edit', compact('book', 'categories', 'locations'));
    }

    public function update(Request $request, Book $book)
    {
        $collectionType = $request->input('collection_type', $book->collection_type);

        // Retain temporary uploaded files across validation failures
        $tempImage = $request->input('temp_image');
        $tempImageName = $request->input('temp_image_name');
        $tempPdf = $request->input('temp_pdf');
        $tempPdfName = $request->input('temp_pdf_name');

        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $tempImage = $request->file('image')->store('temp/books/images', 'public');
            $tempImageName = $request->file('image')->getClientOriginalName();
        }

        if ($request->hasFile('pdf_file') && $request->file('pdf_file')->isValid()) {
            $tempPdf = $request->file('pdf_file')->store('temp/books/pdf', 'local');
            $tempPdfName = $request->file('pdf_file')->getClientOriginalName();
        }

        $rules = [
            'book_code' => ['required', 'string', 'max:50', Rule::unique('books', 'book_code')->ignore($book->id)->whereNull('deleted_at')],
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'collection_type' => 'required|in:fisik,digital,fisik_digital',
            'publisher' => 'nullable|string|max:255',
            'year' => 'nullable|integer|min:1800|max:' . (date('Y') + 1),
            'isbn' => ['nullable', 'string', 'max:50', Rule::unique('books', 'isbn')->ignore($book->id)->whereNull('deleted_at')],
            'language' => 'nullable|string|max:50',
            'page_count' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'fine_type' => 'nullable|in:fixed,multiplier,custom',
            'fine_value' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pdf_file' => 'nullable|file|mimes:pdf|max:51200',
            'remove_pdf' => 'nullable|boolean',
        ];

        if ($collectionType === 'fisik') {
            $rules['stock'] = 'required|integer|min:1';
            $rules['location_id'] = 'required|exists:locations,id';
        } elseif ($collectionType === 'fisik_digital') {
            $rules['stock'] = 'required|integer|min:1';
            $rules['location_id'] = 'required|exists:locations,id';
        }

        $messages = [
            'image.image' => 'Berkas sampul harus berupa file gambar.',
            'image.mimes' => 'Format berkas sampul hanya menerima PNG, JPG, JPEG, atau WEBP. Dokumen PDF tidak diperbolehkan untuk sampul.',
            'image.max' => 'Ukuran berkas sampul buku tidak boleh lebih dari 2MB.',
            'pdf_file.mimes' => 'Berkas naskah digital harus berformat dokumen PDF.',
            'pdf_file.max' => 'Ukuran berkas naskah PDF maksimal 50MB.',
        ];

        try {
            $validated = $request->validate($rules, $messages);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput($request->except(['image', 'pdf_file']) + [
                    'temp_image' => $tempImage,
                    'temp_image_name' => $tempImageName,
                    'temp_pdf' => $tempPdf,
                    'temp_pdf_name' => $tempPdfName,
                ]);
        }

        // Consistency check for PDF requirement on digital/hybrid
        $hasPdfAlready = (!empty($book->pdf_path) && !$request->boolean('remove_pdf')) || (!empty($tempPdf) && Storage::disk('local')->exists($tempPdf));

        if (in_array($collectionType, ['digital', 'fisik_digital']) && !$hasPdfAlready) {
            return redirect()->back()
                ->withInput($request->except(['image', 'pdf_file']) + [
                    'temp_image' => $tempImage,
                    'temp_image_name' => $tempImageName,
                    'temp_pdf' => $tempPdf,
                    'temp_pdf_name' => $tempPdfName,
                ])
                ->with('error', 'Koleksi dengan format ' . ($collectionType === 'digital' ? 'Digital' : 'Fisik & Digital') . ' wajib memiliki berkas naskah PDF.');
        }

        if ($request->title !== $book->title) {
            $validated['slug'] = Str::slug($request->title) . '-' . uniqid();
        }

        $validated['language'] = $validated['language'] ?? 'Indonesia';
        $validated['price'] = $validated['price'] ?? $book->price ?? 0;
        $validated['fine_type'] = $validated['fine_type'] ?? $book->fine_type ?? 'fixed';
        $validated['fine_value'] = $validated['fine_value'] ?? $book->fine_value ?? '50000';

        // Adjust stocks according to collection_type
        if ($collectionType === 'digital') {
            $validated['stock'] = 0;
            $validated['available_stock'] = 0;
            $validated['location_id'] = null;
        } else {
            $newStock = (int) $request->stock;
            $stockDiff = $newStock - $book->stock;
            $validated['stock'] = $newStock;
            $validated['available_stock'] = max(0, $book->available_stock + $stockDiff);
        }

        // Handle cover image
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }
            $path = $request->file('image')->store('books', 'public');
            $validated['image'] = $path;
        } elseif (!empty($tempImage) && Storage::disk('public')->exists($tempImage)) {
            if ($book->image && Storage::disk('public')->exists($book->image)) {
                Storage::disk('public')->delete($book->image);
            }
            $finalImagePath = 'books/' . basename($tempImage);
            Storage::disk('public')->move($tempImage, $finalImagePath);
            $validated['image'] = $finalImagePath;
        } elseif ($request->filled('auto_pdf_cover') && str_starts_with($request->input('auto_pdf_cover'), 'data:image')) {
            try {
                $parts = explode(',', $request->input('auto_pdf_cover'), 2);
                if (isset($parts[1])) {
                    $decoded = base64_decode($parts[1]);
                    if ($decoded !== false) {
                        if ($book->image && Storage::disk('public')->exists($book->image)) {
                            Storage::disk('public')->delete($book->image);
                        }
                        $ext = str_contains($parts[0], 'image/webp') ? 'webp' : 'jpg';
                        $autoCoverPath = 'books/cover_auto_' . uniqid() . '.' . $ext;
                        Storage::disk('public')->put($autoCoverPath, $decoded);
                        $validated['image'] = $autoCoverPath;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Auto cover save failed during update: ' . $e->getMessage());
            }
        }

        // Explicit PDF removal: only when admin explicitly chooses 'remove_pdf'
        if ($request->boolean('remove_pdf')) {
            if ($book->pdf_path) {
                Storage::disk('local')->delete($book->pdf_path);
            }
            $validated['pdf_path'] = null;
        } elseif (!empty($tempPdf) && Storage::disk('local')->exists($tempPdf)) {
            if ($book->pdf_path) {
                Storage::disk('local')->delete($book->pdf_path);
            }
            $finalPdfPath = 'books/pdf/' . basename($tempPdf);
            Storage::disk('local')->move($tempPdf, $finalPdfPath);
            $validated['pdf_path'] = $finalPdfPath;
        }

        $book->update($validated);

        return redirect()->route('admin.books.index')->with('success', 'Data koleksi pustaka berhasil diperbarui!');
    }

    public function destroy(Book $book)
    {
        // Cegah penghapusan jika buku sedang dipinjam aktif
        if ($book->loanDetails()->whereHas('loan', fn($q) => $q->where('status', 'borrowed'))->exists()) {
            return redirect()->back()->with('error', 'Koleksi buku "'.$book->title.'" tidak dapat dihapus karena sedang dalam masa peminjaman aktif oleh anggota.');
        }

        // Cegah penghapusan jika buku memiliki rekaman transaksi sirkulasi
        if ($book->loanDetails()->exists()) {
            return redirect()->back()->with('error', 'Koleksi buku "'.$book->title.'" tidak dapat dihapus karena memiliki riwayat data transaksi sirkulasi peminjaman.');
        }

        $book->delete();
        return redirect()->route('admin.books.index')->with('success', 'Buku berhasil dihapus dari katalog!');
    }

    /**
     * Save auto-rendered PDF page 1 cover image for an existing book.
     */
    public function saveAutoCover(Request $request, Book $book)
    {
        $request->validate([
            'cover_image' => 'nullable|file|image|max:5120',
            'cover_base64' => 'nullable|string',
        ]);

        try {
            $coverPath = null;
            if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
                if ($book->image && Storage::disk('public')->exists($book->image)) {
                    Storage::disk('public')->delete($book->image);
                }
                $coverPath = $request->file('cover_image')->store('books', 'public');
            } elseif ($request->filled('cover_base64') && str_starts_with($request->input('cover_base64'), 'data:image')) {
                if ($book->image && Storage::disk('public')->exists($book->image)) {
                    Storage::disk('public')->delete($book->image);
                }
                $parts = explode(',', $request->input('cover_base64'), 2);
                if (isset($parts[1])) {
                    $decoded = base64_decode($parts[1]);
                    if ($decoded !== false) {
                        $ext = str_contains($parts[0], 'image/webp') ? 'webp' : 'jpg';
                        $filename = 'books/cover_auto_' . $book->id . '_' . uniqid() . '.' . $ext;
                        Storage::disk('public')->put($filename, $decoded);
                        $coverPath = $filename;
                    }
                }
            }

            if ($coverPath) {
                $book->update(['image' => $coverPath]);
                return response()->json([
                    'success' => true,
                    'cover_url' => $book->fresh()->cover_url,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error("Failed to save auto cover for book {$book->id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }

        return response()->json(['success' => false, 'message' => 'Tidak ada gambar sampul valid.'], 400);
    }

    protected function generateUniqueBookCode(): string
    {
        $lastId = Book::withTrashed()->max('id') ?? 0;
        $number = $lastId + 1;
        do {
            $code = 'RPK-B' . str_pad($number, 4, '0', STR_PAD_LEFT);
            $exists = Book::withTrashed()->where('book_code', $code)->exists();
            if ($exists) {
                $number++;
            }
        } while ($exists);
        return $code;
    }
}
