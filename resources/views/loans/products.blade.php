@php
    $loanProducts = $loanProducts ?? $products ?? \App\Models\LoanProduct::where('is_active', true)->get();
    $savingsProducts = $savingsProducts ?? app(\App\Http\Controllers\HomeController::class)->getSavingsProducts();
@endphp

@include('pages.products', ['loanProducts' => $loanProducts, 'savingsProducts' => $savingsProducts])
