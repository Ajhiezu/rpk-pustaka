<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class MemberImportController extends Controller
{
    /**
     * Display the Member Bulk Import upload page.
     */
    public function create(): View
    {
        return view('admin.users.import');
    }

    /**
     * Process uploaded spreadsheet and preview candidates.
     */
    public function preview(Request $request): View|RedirectResponse
    {
        $request->validate([
            'spreadsheet_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        try {
            $file = $request->file('spreadsheet_file');
            $candidates = $this->parseSpreadsheet($file);

            if (empty($candidates)) {
                return redirect()->back()->with('error', 'Spreadsheet tidak memuat data anggota atau format kolom tidak dapat dikenali. Pastikan header kolom sesuai template.');
            }

            // Validate each candidate
            foreach ($candidates as &$cand) {
                $cand = $this->validateCandidate($cand);
            }

            // Store in session
            $batchId = Str::uuid()->toString();
            session(['member_import_batch_' . $batchId => [
                'batch_id'   => $batchId,
                'created_at' => time(),
                'candidates' => $candidates,
            ]]);

            return view('admin.users.import-preview', compact('batchId', 'candidates'));
        } catch (\Throwable $e) {
            Log::error('Member spreadsheet parse error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
        }
    }

    /**
     * Execute final bulk import of member accounts.
     */
    public function store(Request $request): RedirectResponse
    {
        $batchId = preg_replace('/[^a-zA-Z0-9_\-]/', '', $request->input('batch_id'));
        $sessionKey = 'member_import_batch_' . $batchId;
        $batch = session($sessionKey);

        if (!$batch || empty($batch['candidates'])) {
            return redirect()->route('admin.members.import.create')
                ->with('error', 'Sesi import telah kedaluwarsa. Silakan upload kembali file spreadsheet.');
        }

        $submittedCandidates = $request->input('candidates', []);
        $candidates = $batch['candidates'];

        // Merge form overrides
        foreach ($candidates as $idx => &$cand) {
            $override = $submittedCandidates[$idx] ?? [];
            $cand['name']    = trim($override['name'] ?? $cand['name']);
            $cand['email']   = strtolower(trim($override['email'] ?? $cand['email']));
            $cand['role']    = in_array($override['role'] ?? '', ['admin', 'anggota']) ? $override['role'] : ($cand['role'] ?? 'anggota');
            $cand['phone']   = !empty($override['phone']) ? trim($override['phone']) : ($cand['phone'] ?? null);
            $cand['address'] = !empty($override['address']) ? trim($override['address']) : ($cand['address'] ?? null);
        }
        unset($cand);

        $selectedIdxs = $request->input('selected_ids', array_keys($candidates));

        $imported  = 0;
        $duplicate = 0;
        $failed    = 0;
        $failedList = [];

        foreach ($candidates as $idx => $cand) {
            if (!in_array((string)$idx, array_map('strval', $selectedIdxs))) {
                continue;
            }

            if (empty($cand['name']) || empty($cand['email'])) {
                $failed++;
                $failedList[] = ['name' => $cand['name'] ?? '(tanpa nama)', 'reason' => 'Nama atau email kosong'];
                continue;
            }

            // Check duplicate email
            if (User::where('email', $cand['email'])->exists()) {
                $duplicate++;
                continue;
            }

            try {
                $password = !empty($cand['password']) ? $cand['password'] : 'password';

                User::create([
                    'name'     => $cand['name'],
                    'email'    => $cand['email'],
                    'password' => Hash::make($password),
                    'role'     => $cand['role'] ?? 'anggota',
                    'phone'    => !empty($cand['phone']) ? $cand['phone'] : null,
                    'address'  => !empty($cand['address']) ? $cand['address'] : null,
                ]);

                $imported++;
            } catch (\Throwable $e) {
                $failed++;
                $failedList[] = ['name' => $cand['name'], 'reason' => Str::limit($e->getMessage(), 100)];
                Log::error("Member import failed for {$cand['email']}: " . $e->getMessage());
            }
        }

        session()->forget($sessionKey);

        $msg = "Import Anggota Selesai: {$imported} akun berhasil dibuat";
        if ($duplicate > 0) $msg .= ", {$duplicate} email sudah terdaftar (dilewati)";
        if ($failed > 0) {
            $reasons = implode('; ', array_map(fn($f) => "• {$f['name']}: {$f['reason']}", array_slice($failedList, 0, 3)));
            $msg .= ", {$failed} gagal ({$reasons})";
        }
        $msg .= '.';

        if ($imported > 0) {
            return redirect()->route('admin.users.index', ['role' => 'anggota'])
                ->with('success', $msg);
        }

        return redirect()->route('admin.members.import.create')
            ->with('error', $msg);
    }

    /**
     * Download the Excel template for member import.
     */
    public function downloadTemplate(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template_import_anggota.csv"',
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($handle, ['nama', 'email', 'role', 'no_telepon', 'alamat']);
            fputcsv($handle, ['Ahmad Fauzi', 'ahmad.fauzi@example.com', 'anggota', '081234567890', 'Jl. Contoh No. 1, Yogyakarta']);
            fputcsv($handle, ['Siti Rahayu', 'siti.rahayu@example.com', 'anggota', '082345678901', 'Jl. Mawar No. 5, Jakarta']);
            fclose($handle);
        }, 200, $headers);
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Parse uploaded spreadsheet file (xlsx/xls/csv) into candidate rows.
     */
    private function parseSpreadsheet($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $path = $file->getRealPath();

        if (in_array($extension, ['csv', 'txt'])) {
            return $this->parseCsv($path);
        }

        // Use PhpSpreadsheet for xlsx/xls
        $spreadsheet = IOFactory::load($path);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) < 2) {
            return [];
        }

        // Normalize headers from first row
        $headerRow = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
        $columnMap = $this->buildColumnMap($headerRow);

        $candidates = [];
        foreach (array_slice($rows, 1) as $idx => $row) {
            $cand = $this->mapRowToCandidate($row, $columnMap);
            if (!empty($cand['name']) || !empty($cand['email'])) {
                $candidates[$idx] = $cand;
            }
        }

        return $candidates;
    }

    private function parseCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        if (!$handle) return [];

        // Detect BOM
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            fseek($handle, 0);
        }

        $headerRow = fgetcsv($handle);
        if (!$headerRow) return [];
        $headerRow = array_map(fn($h) => strtolower(trim((string)$h)), $headerRow);
        $columnMap = $this->buildColumnMap($headerRow);

        $candidates = [];
        $idx = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $cand = $this->mapRowToCandidate($row, $columnMap);
            if (!empty($cand['name']) || !empty($cand['email'])) {
                $candidates[$idx++] = $cand;
            }
        }

        fclose($handle);
        return $candidates;
    }

    private function buildColumnMap(array $headers): array
    {
        $map = [];
        $nameAliases     = ['nama', 'name', 'nama lengkap', 'full name', 'username'];
        $emailAliases    = ['email', 'e-mail', 'alamat email', 'email address'];
        $roleAliases     = ['role', 'peran', 'hak akses', 'tipe', 'type'];
        $phoneAliases    = ['no_telepon', 'phone', 'telepon', 'no telepon', 'hp', 'nomor hp', 'handphone', 'no. hp'];
        $addressAliases  = ['alamat', 'address', 'domisili', 'tempat tinggal'];
        $passwordAliases = ['password', 'kata_sandi', 'pass', 'kata sandi'];

        foreach ($headers as $i => $h) {
            if (in_array($h, $nameAliases))     $map['name']     = $i;
            if (in_array($h, $emailAliases))    $map['email']    = $i;
            if (in_array($h, $roleAliases))     $map['role']     = $i;
            if (in_array($h, $phoneAliases))    $map['phone']    = $i;
            if (in_array($h, $addressAliases))  $map['address']  = $i;
            if (in_array($h, $passwordAliases)) $map['password'] = $i;
        }

        return $map;
    }

    private function mapRowToCandidate(array $row, array $columnMap): array
    {
        $get = fn($key) => isset($columnMap[$key]) ? trim((string)($row[$columnMap[$key]] ?? '')) : '';

        $role = strtolower($get('role'));
        if (!in_array($role, ['admin', 'anggota'])) {
            $role = 'anggota';
        }

        return [
            'name'     => $get('name'),
            'email'    => strtolower($get('email')),
            'role'     => $role,
            'phone'    => $get('phone') ?: null,
            'address'  => $get('address') ?: null,
            'password' => $get('password') ?: null,
        ];
    }

    private function validateCandidate(array $cand): array
    {
        $cand['errors'] = [];
        $cand['status'] = 'OK';

        if (empty($cand['name'])) {
            $cand['errors'][] = 'Nama wajib diisi';
            $cand['status'] = 'ERROR';
        }

        if (empty($cand['email'])) {
            $cand['errors'][] = 'Email wajib diisi';
            $cand['status'] = 'ERROR';
        } elseif (!filter_var($cand['email'], FILTER_VALIDATE_EMAIL)) {
            $cand['errors'][] = 'Format email tidak valid';
            $cand['status'] = 'ERROR';
        } elseif (User::where('email', $cand['email'])->exists()) {
            $cand['errors'][] = 'Email sudah terdaftar di sistem';
            $cand['status'] = 'DUPLICATE';
        }

        return $cand;
    }
}
