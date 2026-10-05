<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('loans:auto-cancel')]
#[Description('Cancel loans that are not picked up on time')]
class AutoCancelLoans extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(\App\Services\LibraryService $libraryService)
    {
        $count = $libraryService->cancelUnclaimedLoans();
        $this->info("Berhasil membatalkan {$count} peminjaman yang kedaluwarsa.");
    }
}

