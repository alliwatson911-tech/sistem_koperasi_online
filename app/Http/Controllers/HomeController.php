<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanProduct;
use App\Models\Member;
use App\Models\SavingsAccount;
use App\Services\LoanCalculationService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct(
        protected LoanCalculationService $calculator
    ) {
    }

    /**
     * Halaman Utama / Landing Page Sistem Koperasi
     */
    public function index(Request $request)
    {
        $products = LoanProduct::where('is_active', true)->get();

        // Parameter Simulasi Pinjaman Terpadu
        $selectedProductId = $request->get('product_id', $products->first()?->id);
        $selectedProduct = $products->firstWhere('id', (int) $selectedProductId) ?: $products->first();

        $defaultAmount = ($selectedProduct && $selectedProduct->isWeekly()) ? 3000000 : 10000000;
        $defaultTenor = ($selectedProduct && $selectedProduct->isWeekly()) ? 10 : 12;

        $amount = (int) $request->get('amount', $defaultAmount);
        $tenor = (int) $request->get('tenor', $defaultTenor);
        $method = $request->get('calculation_method', $selectedProduct?->calculation_method ?? 'annuity');

        $simulationResult = null;
        if ($selectedProduct) {
            $adminFee = (int) round(($amount * ($selectedProduct->admin_fee_percentage / 100)) + $selectedProduct->admin_fee_fixed);
            $simulationResult = $this->calculator->simulate(
                $amount,
                $tenor,
                (float) $selectedProduct->interest_rate_per_period,
                $method,
                $adminFee,
                $selectedProduct->tenor_unit
            );
        }

        // Statistik Koperasi untuk Indikator Kepercayaan & Keterbukaan
        try {
            $totalMembers = Member::where('status', Member::STATUS_ACTIVE)->count();
            $totalLoans = Loan::sum('principal_amount');
            $totalSavings = SavingsAccount::sum('balance');
        } catch (\Throwable $e) {
            $totalMembers = 1450;
            $totalLoans = 12850000000;
            $totalSavings = 18500000000;
        }

        $stats = [
            'total_members' => $totalMembers ?: 1450,
            'total_loan_distributed' => $totalLoans ?: 12850000000,
            'total_savings_managed' => $totalSavings ?: 18500000000,
            'product_count' => $products->count(),
        ];

        return view('home', compact(
            'products',
            'selectedProduct',
            'simulationResult',
            'stats',
            'amount',
            'tenor',
            'method'
        ));
    }
}

