<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'interest_rate_annual',
        'interest_rate_monthly',
        'interest_rate_per_period',
        'calculation_method',
        'tenor_unit',
        'min_amount',
        'max_amount',
        'min_tenor_months',
        'max_tenor_months',
        'min_tenor',
        'max_tenor',
        'admin_fee_percentage',
        'admin_fee_fixed',
        'savings_percentage',
        'penalty_rate_daily',
        'is_active',
    ];

    protected $casts = [
        'interest_rate_annual' => 'decimal:2',
        'interest_rate_monthly' => 'decimal:2',
        'interest_rate_per_period' => 'decimal:2',
        'min_amount' => 'integer',
        'max_amount' => 'integer',
        'min_tenor_months' => 'integer',
        'max_tenor_months' => 'integer',
        'min_tenor' => 'integer',
        'max_tenor' => 'integer',
        'admin_fee_percentage' => 'decimal:2',
        'admin_fee_fixed' => 'integer',
        'savings_percentage' => 'decimal:2',
        'penalty_rate_daily' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function isWeekly(): bool
    {
        return $this->tenor_unit === 'week';
    }

    public function isDaily(): bool
    {
        return $this->tenor_unit === 'day';
    }

    public function isMonthly(): bool
    {
        return $this->tenor_unit === 'month' || (!$this->isWeekly() && !$this->isDaily());
    }

    public function isBusinessLoan(): bool
    {
        return $this->code === 'PRD-MDR' || str_contains(strtolower($this->name), 'modal') || str_contains(strtolower($this->name), 'usaha');
    }

    public function getTenorUnitLabelAttribute(): string
    {
        if ($this->isDaily()) {
            return 'Hari';
        }
        return $this->isWeekly() ? 'Minggu' : 'Bulan';
    }

    public function getMinTenorAttribute($value): int
    {
        return $value ?? $this->min_tenor_months ?? 1;
    }

    public function getMaxTenorAttribute($value): int
    {
        return $value ?? $this->max_tenor_months ?? 60;
    }

    public function getInterestRatePerPeriodAttribute($value): float
    {
        return (float) ($value ?? $this->interest_rate_monthly ?? 0);
    }

    public function getSavingsPercentageAttribute($value): float
    {
        return (float) ($value ?? ($this->isWeekly() ? 5.00 : 0.00));
    }

    public function calculateAdminFee(int $amount): int
    {
        return (int) round(($amount * ($this->admin_fee_percentage / 100)) + $this->admin_fee_fixed);
    }

    public function calculateSavingsDeduction(int $amount): int
    {
        return (int) round($amount * ($this->savings_percentage / 100));
    }

    public function calculateNetDisbursement(int $amount): int
    {
        return max(0, $amount - $this->calculateAdminFee($amount) - $this->calculateSavingsDeduction($amount));
    }

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }
}
