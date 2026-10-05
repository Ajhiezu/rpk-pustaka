<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LibraryService;

class ExpireReservationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rpk:expire-loan-reservations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memproses otomatis reservasi peminjaman fisik yang melebihi batas waktu pengambilan (pickup deadline)';

    /**
     * Execute the console command.
     */
    public function handle(LibraryService $libraryService)
    {
        $this->info('Memulai pengecekan reservasi kedaluwarsa...');
        $count = $libraryService->expireAllOverdueReservations();
        $this->info("Selesai. Berhasil memperbarui {$count} reservasi kedaluwarsa dan mengembalikan stok fisik.");

        return Command::SUCCESS;
    }
}
