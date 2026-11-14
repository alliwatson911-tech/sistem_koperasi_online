<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SavingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public & Webhook Routes
|--------------------------------------------------------------------------
*/
Route::post('/webhook/payment/{gateway?}', [PaymentGatewayController::class, 'webhook'])->name('payments.webhook')->middleware('throttle:120,1');
Route::post('/api/webhook/payment/{gateway?}', [PaymentGatewayController::class, 'webhook'])->middleware('throttle:120,1');

/*
|--------------------------------------------------------------------------
| Public & Guest Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post')->middleware('throttle:10,1');
    Route::get('/daftar', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/daftar', [AuthController::class, 'register'])->name('register.post')->middleware('throttle:10,1');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Halaman Publik (Navigasi Utama Landing & Edukasi Anggota)
Route::get('/produk', [HomeController::class, 'products'])->name('products.index');
Route::get('/produk-pinjaman', [HomeController::class, 'products'])->name('loans.products');
Route::get('/panduan', [HomeController::class, 'guide'])->name('guide');
Route::get('/simulasi', [LoanController::class, 'simulation'])->name('loans.simulation.alias');
Route::get('/simulasi-pinjaman', [LoanController::class, 'simulation'])->name('loans.simulation');
Route::get('/tentang-kami', [HomeController::class, 'about'])->name('about');

/*
|--------------------------------------------------------------------------
| Authenticated Routes (Semua Pengguna Terdaftar)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil-saya', [MemberController::class, 'profile'])->name('members.profile');

    // Modul Simpanan
    Route::get('/simpanan', [SavingsController::class, 'index'])->name('savings.index');
    Route::get('/simpanan/{account}', [SavingsController::class, 'show'])->name('savings.show');
    Route::get('/simpanan/{account}/setor', [SavingsController::class, 'depositForm'])->name('savings.deposit.form');
    Route::post('/simpanan/{account}/setor', [SavingsController::class, 'deposit'])->name('savings.deposit.submit');
    Route::get('/simpanan/{account}/tarik', [SavingsController::class, 'withdrawForm'])->name('savings.withdraw.form');
    Route::post('/simpanan/{account}/tarik', [SavingsController::class, 'withdraw'])->name('savings.withdraw.submit');

    // Modul Pinjaman Anggota
    Route::get('/pinjaman-saya', [LoanController::class, 'myLoans'])->name('loans.my');
    Route::get('/ajukan-pinjaman', [LoanController::class, 'applyForm'])->name('loans.apply');
    Route::post('/ajukan-pinjaman', [LoanController::class, 'apply'])->name('loans.apply.submit');
    Route::post('/pengajuan-pinjaman/{application}/kirim-ulang', [LoanController::class, 'resubmitApplication'])->name('loans.applications.resubmit');
    Route::get('/pinjaman/{loan}', [LoanController::class, 'show'])->name('loans.show');
    Route::post('/pinjaman/{loan}/akad', [LoanController::class, 'signAkad'])->name('loans.sign_akad');
    Route::get('/pinjaman/{loan}/bayar', [LoanController::class, 'payForm'])->name('loans.pay');
    Route::post('/pinjaman/{loan}/bayar', [LoanController::class, 'pay'])->name('loans.pay.submit');
    Route::post('/pinjaman/{loan}/bayar-gateway', [PaymentGatewayController::class, 'checkoutLoan'])->name('loans.pay.gateway')->middleware('throttle:30,1');
    Route::post('/simpanan/{account}/setor-gateway', [PaymentGatewayController::class, 'checkoutSavings'])->name('savings.deposit.gateway')->middleware('throttle:30,1');
    Route::post('/pinjaman/{loan}/pelunasan-dipercepat', [LoanController::class, 'earlyRepay'])->name('loans.early_repay');
    Route::post('/pinjaman/{loan}/ajukan-keringanan', [LoanController::class, 'requestRestructuring'])->name('loans.restructuring.request');

    // Modul Tagihan & Pembayaran Gateway (Anggota)
    Route::get('/pembayaran/invoice/{orderId}', [PaymentGatewayController::class, 'showInvoice'])->name('payments.invoice');
    Route::get('/pembayaran/invoice/{orderId}/status', [PaymentGatewayController::class, 'checkInvoiceStatus'])->name('payments.invoice.status');
    Route::get('/pembayaran/kuitansi/{orderId}', [PaymentGatewayController::class, 'receipt'])->name('payments.receipt');

    /*
    |--------------------------------------------------------------------------
    | Modul Petugas & Pengurus (Staff)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:teller,analyst,manager,admin'])->group(function () {
        // Kelola Anggota
        Route::get('/anggota', [MemberController::class, 'index'])->name('members.index');
        Route::get('/anggota/{member}', [MemberController::class, 'show'])->name('members.show');
        Route::post('/anggota/{member}/verifikasi', [MemberController::class, 'verify'])->name('members.verify');

        // Kelola Pinjaman & Pengajuan
        Route::get('/kelola-pinjaman', [LoanController::class, 'indexLoans'])->name('loans.manage');
        Route::get('/portofolio-pinjaman', [LoanController::class, 'indexLoans'])->name('loans.index');
        Route::get('/pengajuan-pinjaman', [LoanController::class, 'applications'])->name('loans.applications');
        Route::get('/pengajuan-pinjaman/{application}', [LoanController::class, 'showApplication'])->name('loans.applications.show');
        Route::post('/pengajuan-pinjaman/{application}/klaim', [LoanController::class, 'claimApplication'])->name('loans.applications.claim');
        Route::post('/pengajuan-pinjaman/{application}/terima', [LoanController::class, 'receiveApplication'])->name('loans.applications.receive');
        Route::post('/pengajuan-pinjaman/{application}/minta-revisi', [LoanController::class, 'requestRevisionApplication'])->name('loans.applications.request_revision');
        Route::post('/pengajuan-pinjaman/{application}/alihkan-teller', [LoanController::class, 'reassignTeller'])->name('loans.applications.reassign_teller');
        Route::post('/pengajuan-pinjaman/{application}/analisis', [LoanController::class, 'reviewApplication'])->name('loans.applications.review');
        Route::post('/pengajuan-pinjaman/{application}/persetujuan', [LoanController::class, 'approveApplication'])->name('loans.applications.approve');
        Route::post('/pinjaman/{loan}/cairkan', [LoanController::class, 'disburse'])->name('loans.disburse');

        // Modul Manajemen Tunggakan & Jatuh Tempo (Kolektibilitas & Penagihan)
        Route::get('/kelola-tunggakan', [LoanController::class, 'manageOverdue'])->name('loans.overdue.manage');
        Route::post('/kelola-tunggakan/sinkronkan', [LoanController::class, 'syncOverdue'])->name('loans.overdue.sync');
        Route::post('/pinjaman/{loan}/catat-penagihan', [LoanController::class, 'recordCollection'])->name('loans.collection.record');

        // Laporan Operasional
        Route::get('/laporan/simpanan', [ReportController::class, 'savings'])->name('reports.savings');
        Route::get('/laporan/pinjaman', [ReportController::class, 'loans'])->name('reports.loans');
        Route::get('/laporan/tunggakan', [ReportController::class, 'overdue'])->name('reports.overdue');
        Route::get('/laporan/arus-kas', [ReportController::class, 'cashFlow'])->name('reports.cash_flow');

        // Modul Manajemen Payment Gateway & Rekonsiliasi
        Route::get('/kelola-pembayaran', [PaymentGatewayController::class, 'staffDashboard'])->name('payments.dashboard');
        Route::post('/kelola-pembayaran/{payment}/rekonsiliasi', [PaymentGatewayController::class, 'staffReconcile'])->name('payments.reconcile');
        Route::get('/kelola-pembayaran/ekspor', [PaymentGatewayController::class, 'export'])->name('payments.export');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Akuntansi & Tutup Buku (Admin & Manajer)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:manager,admin'])->group(function () {
        Route::post('/kelola-tunggakan/{restructure}/putusan', [LoanController::class, 'decideRestructuring'])->name('loans.restructuring.decide');
        Route::post('/kelola-pembayaran/settlement', [PaymentGatewayController::class, 'staffCreateSettlement'])->name('payments.settlement');
        Route::get('/akuntansi/jurnal', [AccountingController::class, 'journals'])->name('accounting.journals');
        Route::get('/akuntansi/buku-besar', [AccountingController::class, 'ledger'])->name('accounting.ledger');
        Route::get('/akuntansi/neraca-saldo', [AccountingController::class, 'trialBalance'])->name('accounting.trial_balance');
        Route::get('/akuntansi/shu', [AccountingController::class, 'shu'])->name('accounting.shu');
        Route::post('/akuntansi/shu/tutup-buku', [AccountingController::class, 'closeShu'])->name('accounting.shu.close');
    });

    /*
    |--------------------------------------------------------------------------
    | Modul Pengaturan & Publikasi Informasi (Khusus Admin)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin/billboard', [\App\Http\Controllers\Admin\BillboardController::class, 'index'])->name('admin.billboards.index');
        Route::get('/admin/billboard/tambah', [\App\Http\Controllers\Admin\BillboardController::class, 'create'])->name('admin.billboards.create');
        Route::post('/admin/billboard', [\App\Http\Controllers\Admin\BillboardController::class, 'store'])->name('admin.billboards.store');
        Route::get('/admin/billboard/{billboard}/edit', [\App\Http\Controllers\Admin\BillboardController::class, 'edit'])->name('admin.billboards.edit');
        Route::put('/admin/billboard/{billboard}', [\App\Http\Controllers\Admin\BillboardController::class, 'update'])->name('admin.billboards.update');
        Route::delete('/admin/billboard/{billboard}', [\App\Http\Controllers\Admin\BillboardController::class, 'destroy'])->name('admin.billboards.destroy');
        Route::post('/admin/billboard/{billboard}/toggle', [\App\Http\Controllers\Admin\BillboardController::class, 'toggle'])->name('admin.billboards.toggle');
    });
});
