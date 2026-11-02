<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    public const STATUS_AWAITING_DISBURSEMENT = 'approved_awaiting_disbursement';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_PAID_OFF = 'paid_off';
    public const STATUS_WRITTEN_OFF = 'written_off';

    protected $fillable = [
        'loan_application_id',
        'member_id',
        'loan_product_id',
        'loan_no',
        'principal_amount',
        'total_interest',
        'total_admin_fee',
        'total_loan_amount',
        'outstanding_principal',
        'outstanding_interest',
        'outstanding_penalty',
        'tenor_unit',
        'tenor_months',
        'tenor_count',
        'interest_rate_monthly',
        'interest_rate_per_period',
        'calculation_method',
        'status',
        'agreed_at',
        'disbursed_at',
        'disbursed_by',
        'maturity_date',
    ];

    protected $casts = [
        'principal_amount' => 'integer',
        'total_interest' => 'integer',
        'total_admin_fee' => 'integer',
        'total_loan_amount' => 'integer',
        'outstanding_principal' => 'integer',
        'outstanding_interest' => 'integer',
        'outstanding_penalty' => 'integer',
        'tenor_months' => 'integer',
        'tenor_count' => 'integer',
        'interest_rate_monthly' => 'decimal:2',
        'interest_rate_per_period' => 'decimal:2',
        'agreed_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'maturity_date' => 'date',
    ];

    public function isWeekly(): bool
    {
        return $this->tenor_unit === 'week';
    }

    public function isMonthly(): bool
    {
        return $this->tenor_unit !== 'week';
    }

    public function getTenorUnitLabelAttribute(): string
    {
        return $this->isWeekly() ? 'Minggu' : 'Bulan';
    }

    public function getTenorCountAttribute($value): int
    {
        return $value ?? $this->tenor_months ?? 1;
    }

    public function getTenorDisplayAttribute(): string
    {
        return "{$this->tenor_count} {$this->tenor_unit_label}";
    }

    public function getInterestRatePerPeriodAttribute($value): float
    {
        return (float) ($value ?? $this->interest_rate_monthly ?? 0);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function loanProduct()
    {
        return $this->belongsTo(LoanProduct::class);
    }

    public function loanApplication()
    {
        return $this->belongsTo(LoanApplication::class);
    }

    public function disburser()
    {
        return $this->belongsTo(User::class, 'disbursed_by');
    }

    public function schedules()
    {
        return $this->hasMany(LoanSchedule::class)->orderBy('installment_no');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class)->orderBy('payment_date', 'desc');
    }

    public function gatewayPayments()
    {
        return $this->morphMany(GatewayPayment::class, 'payable')->orderBy('id', 'desc');
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function getNextUnpaidSchedule()
    {
        return $this->schedules()->whereIn('status', ['unpaid', 'partial', 'overdue'])->first();
    }

    public function getReceivedByAttribute(): ?User
    {
        return $this->loanApplication?->receivedBy;
    }

    public function getReceivedAtAttribute(): ?\Carbon\Carbon
    {
        return $this->loanApplication?->received_at;
    }

    public function getReceptionNoteAttribute(): ?string
    {
        return $this->loanApplication?->reception_note;
    }

    public function getReceptionChecklistAttribute(): ?array
    {
        return $this->loanApplication?->reception_checklist;
    }

    public function getTellerDisplayAttribute(): ?string
    {
        return $this->loanApplication?->teller_display;
    }
}
