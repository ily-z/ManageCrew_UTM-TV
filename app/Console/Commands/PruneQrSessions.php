<?php

namespace App\Console\Commands;

use App\Models\QrSession;
use Illuminate\Console\Command;

class PruneQrSessions extends Command
{
    protected $signature = 'qr:prune';

    protected $description = 'Hapus sesi QR yang sudah lewat lebih dari 1 hari';

    public function handle(): int
    {
        $count = QrSession::where('expired_at', '<', now()->subDay())->delete();

        $this->info("{$count} sesi QR lama dihapus.");

        return self::SUCCESS;
    }
}
