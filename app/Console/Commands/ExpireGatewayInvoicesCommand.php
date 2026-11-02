<?php

namespace App\Console\Commands;

use App\Services\GatewayPaymentService;
use Illuminate\Console\Command;

class ExpireGatewayInvoicesCommand extends Command
{
    protected $signature = 'koperasi:expire-gateway-invoices';
    protected $description = 'Periksa dan ubah status tagihan gateway pending yang sudah melewati batas waktu menjadi expired.';

    public function handle(GatewayPaymentService $paymentService): int
    {
        $this->info('Memeriksa tagihan payment gateway yang kedaluwarsa...');
        $count = $paymentService->expireOverdueInvoices();
        $this->info("Pemeriksaan selesai. Sebanyak {$count} tagihan kedaluwarsa telah diperbarui.");
        return self::SUCCESS;
    }
}

