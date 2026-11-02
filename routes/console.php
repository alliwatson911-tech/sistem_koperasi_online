<?php

use App\Services\LoanService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('koperasi:update-overdue', function (LoanService $loanService) {
    $this->info('Memulai pembaruan status keterlambatan dan perhitungan denda pinjaman...');
    $count = $loanService->updateOverdueAndPenalties();
    $this->info("Pembaruan selesai. Sebanyak {$count} jadwal angsuran diperbarui menjadi berstatus menunggak (overdue).");
})->purpose('Periksa jatuh tempo angsuran dan hitung denda keterlambatan harian.');

// Jadwalkan otomatis berjalan setiap pergantian hari pukul 00:05 WITA
Schedule::command('koperasi:update-overdue')->dailyAt('00:05')->withoutOverlapping();

// Jadwalkan pemeriksaan batas waktu penerimaan pengajuan oleh teller setiap jam kerja
Schedule::command('koperasi:check-reception-deadline')->hourly()->withoutOverlapping();

// Jadwalkan pemeriksaan kedaluwarsa tagihan payment gateway setiap 15 menit
Schedule::command('koperasi:expire-gateway-invoices')->everyFifteenMinutes()->withoutOverlapping();

// Jadwalkan rekonsiliasi transaksi pending ke payment gateway setiap 10 menit
Schedule::command('koperasi:reconcile-gateway')->everyTenMinutes()->withoutOverlapping();
