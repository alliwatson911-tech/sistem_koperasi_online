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
        'savings_percentage',
        'total_savings_deduction',
        'net_disbursement_amount',
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
        'business_name',
        'business_type',
        'business_address',
        'business_duration_years',
        'business_monthly_revenue',
        'business_photo_path',
        'ktp_selfie_path',
        'survey_photo_path',
        'collectibility_status',
        'collection_stage',
        'last_collected_at',
        'promised_payment_date',
        'promised_payment_amount',
    ];

    protected $casts = [
        'principal_amount' => 'integer',
        'total_interest' => 'integer',
        'total_admin_fee' => 'integer',
        'savings_percentage' => 'decimal:2',
        'total_savings_deduction' => 'integer',
        'net_disbursement_amount' => 'integer',
        'total_loan_amount' => 'integer',
        'outstanding_principal' => 'integer',
        'outstanding_interest' => 'integer',
        'outstanding_penalty' => 'integer',
        'tenor_months' => 'integer',
        'tenor_count' => 'integer',
        'business_duration_years' => 'integer',
        'business_monthly_revenue' => 'integer',
        'promised_payment_amount' => 'integer',
        'interest_rate_monthly' => 'decimal:2',
        'interest_rate_per_period' => 'decimal:2',
        'agreed_at' => 'datetime',
        'disbursed_at' => 'datetime',
        'last_collected_at' => 'datetime',
        'maturity_date' => 'date',
        'promised_payment_date' => 'date',
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

    public function getTenorUnitLabelAttribute(): string
    {
        if ($this->isDaily()) {
            return 'Hari';
        }
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

    public function collectionLogs()
    {
        return $this->hasMany(LoanCollectionLog::class)->orderBy('action_date', 'desc');
    }

    public function restructuringRequests()
    {
        return $this->hasMany(LoanRestructuringRequest::class)->orderBy('id', 'desc');
    }

    public function isBusinessLoan(): bool
    {
        if ($this->loanProduct) {
            if ($this->loanProduct->code === 'PRD-MDR') {
                return true;
            }
            if (in_array($this->loanProduct->code, ['PRD-KNS', 'PRD-KPT', 'PRD-KNS-DAY', 'PRD-MLT-DAY'])) {
                return false;
            }
        }
        return !empty($this->business_name) || !empty($this->business_photo_path);
    }

    public function getMaxDaysOverdue(): int
    {
        return (int) $this->schedules()->where('status', LoanSchedule::STATUS_OVERDUE)->max('days_overdue');
    }

    public function calculateCollectibility(): string
    {
        $maxDays = $this->getMaxDaysOverdue();
        if ($maxDays <= 0) {
            return 'lancar'; // Kol 1
        } elseif ($maxDays <= 30) {
            return 'dpk'; // Kol 2: Dalam Perhatian Khusus
        } elseif ($maxDays <= 60) {
            return 'kurang_lancar'; // Kol 3
        } elseif ($maxDays <= 90) {
            return 'diragukan'; // Kol 4
        } else {
            return 'macet'; // Kol 5 (NPL)
        }
    }

    public function getCollectibilityLabelAttribute(): string
    {
        return match ($this->collectibility_status ?? $this->calculateCollectibility()) {
            'lancar' => 'Kol-1: Lancar',
            'dpk' => 'Kol-2: Dalam Perhatian Khusus (DPK)',
            'kurang_lancar' => 'Kol-3: Kurang Lancar (1-60 Hari)',
            'diragukan' => 'Kol-4: Diragukan (61-90 Hari)',
            'macet' => 'Kol-5: Macet / NPL (>90 Hari)',
            default => 'Lancar',
        };
    }

    public function getCollectibilityBadgeClassAttribute(): string
    {
        return match ($this->collectibility_status ?? $this->calculateCollectibility()) {
            'lancar' => 'badge-success',
            'dpk' => 'badge-warning',
            'kurang_lancar' => 'badge-warning',
            'diragukan' => 'badge-danger',
            'macet' => 'badge-danger',
            default => 'badge-gray',
        };
    }
}
