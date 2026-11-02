<?php

namespace App\Console\Commands;

use App\Services\GatewayPaymentService;
use Illuminate\Console\Command;

class ReconcilePendingPaymentsCommand extends Command
{
    protected $signature = 'koperasi:reconcile-gateway';
    protected $description = 'Rekonsiliasi transaksi payment gateway pending secara berkala ke API gateway.';

    public function handle(GatewayPaymentService $paymentService): int
    {
        $this->info('Memulai rekonsiliasi transaksi pending ke payment gateway...');
        $count = $paymentService->reconcilePendingPayments();
        $this->info("Rekonsiliasi selesai. Sebanyak {$count} transaksi berhasil diperbarui statusnya.");
        return self::SUCCESS;
    }
}

