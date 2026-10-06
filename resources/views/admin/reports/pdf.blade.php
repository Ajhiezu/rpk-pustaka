<!DOCTYPE html>
<html>
<head>
    <title>Laporan Rekapitulasi Sirkulasi, RPK PUSTAKA IMM SAINTEKMU</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #181818; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 24px; border-bottom: 2px solid #C62828; padding-bottom: 12px; }
        .header h1 { margin: 0; color: #C62828; font-size: 18px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
        .header p { margin: 4px 0 0; color: #666666; font-size: 12px; }
        .header .meta { font-size: 9px; color: #888888; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 10px; border: 1px solid #E5E5E5; text-align: left; vertical-align: middle; }
        th { background-color: #F8F8F7; color: #181818; font-weight: bold; text-transform: uppercase; font-size: 9px; letter-spacing: 0.5px; }
        .footer { text-align: right; margin-top: 30px; font-size: 9px; color: #888888; border-top: 1px solid #E5E5E5; padding-top: 8px; }
        .status { padding: 3px 6px; border-radius: 3px; font-size: 8px; font-weight: bold; text-transform: uppercase; display: inline-block; }
        .borrowed { background-color: #FEF2F2; color: #C62828; }
        .returned { background-color: #EDF7ED; color: #2E7D32; }
        .overdue { background-color: #FDEDED; color: #D32F2F; }
        .cancelled { background-color: #F8F8F7; color: #666666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RPK PUSTAKA IMM SAINTEKMU</h1>
        <p>Laporan Rekapitulasi Transaksi Sirkulasi Koleksi</p>
        <div class="meta">Dicetak pada: {{ now()->format('d M Y') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">Kode / ID</th>
                <th style="width: 20%;">Peminjam</th>
                <th style="width: 28%;">Koleksi Buku</th>
                <th style="width: 14%;">Tgl Pinjam</th>
                <th style="width: 14%;">Tgl Jatuh Tempo</th>
                <th style="width: 12%;">Denda</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $loan)
            <tr>
                <td><strong>{{ $loan->loan_code ?? '#'.$loan->id }}</strong></td>
                <td>
                    <div><strong>{{ $loan->user->name }}</strong></div>
                    <div style="font-size: 9px; color: #666;">{{ $loan->user->email }}</div>
                </td>
                <td>{{ $loan->loanDetails->map(fn($d) => $d->book->title)->implode(', ') }}</td>
                <td>{{ $loan->loan_date ? \Carbon\Carbon::parse($loan->loan_date)->format('d M Y') : '-' }}</td>
                <td>{{ $loan->due_date ? \Carbon\Carbon::parse($loan->due_date)->format('d M Y') : '-' }}</td>
                <td>Rp {{ number_format($loan->fine_amount ?? 0, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #888; padding: 20px;">Tidak ada rekaman data sirkulasi.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>RPK PUSTAKA IMM SAINTEKMU, Sistem Perpustakaan Digital Terintegrasi</p>
    </div>
</body>
</html>
