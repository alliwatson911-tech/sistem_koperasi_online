<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanApplication extends Model
{
    use HasFactory;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_RECEIVED = 'received';
    public const STATUS_NEEDS_REVISION = 'needs_revision';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'member_id',
        'loan_product_id',
        'application_no',
        'amount',
        'tenor_unit',
        'tenor_months',
        'tenor_count',
        'purpose',
        'calculation_method',
        'interest_rate_monthly',
        'interest_rate_per_period',
        'admin_fee',
        'monthly_installment_estimate',
        'status',
        'submitted_at',
        'notes',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'assignment_notes',
        'received_by',
        'received_at',
        'reception_checklist',
        'reception_note',
        'reception_status',
        'revision_requested_at',
        'revision_notes',
        'business_name',
        'business_type',
        'business_address',
        'business_duration_years',
        'business_monthly_revenue',
        'business_photo_path',
        'survey_photo_path',
        'survey_notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'tenor_months' => 'integer',
        'tenor_count' => 'integer',
        'interest_rate_monthly' => 'decimal:2',
        'interest_rate_per_period' => 'decimal:2',
        'admin_fee' => 'integer',
        'monthly_installment_estimate' => 'integer',
        'business_duration_years' => 'integer',
        'business_monthly_revenue' => 'integer',
        'submitted_at' => 'datetime',
        'assigned_at' => 'datetime',
        'received_at' => 'datetime',
        'revision_requested_at' => 'datetime',
        'reception_checklist' => 'array',
    ];

    public function isBusinessLoan(): bool
    {
        if ($this->loanProduct && in_array($this->loanProduct->code, ['PRD-MDR', 'PRD-MGN'])) {
            return true;
        }
        $purpose = strtolower((string) $this->purpose);
        return str_contains($purpose, 'modal') || str_contains($purpose, 'usaha') || str_contains($purpose, 'dagang') || !empty($this->business_name);
    }

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

    public function approvals()
    {
        return $this->hasMany(LoanApproval::class);
    }

    public function loan()
    {
        return $this->hasOne(Loan::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function receivedBy()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Format tampilan identitas teller penerima untuk anggota / audit.
     */
    public function getTellerDisplayAttribute(): ?string
    {
        if (!$this->receivedBy) {
            return null;
        }

        $mode = Setting::get('teller_display_mode', 'name_and_code');
        $teller = $this->receivedBy;

        return match ($mode) {
            'code_only' => $teller->employee_code_formatted,
            'first_name_code' => explode(' ', $teller->name)[0] . " ({$teller->employee_code_formatted})",
            default => "{$teller->name} ({$teller->employee_code_formatted})",
        };
    }

    public static function defaultChecklistItems(): array
    {
        return [
            'ktp' => [
                'label' => 'KTP / Identitas Sah Pemohon Asli & Berlaku',
                'description' => 'Kesesuaian NIK, foto wajah, nama lengkap, dan data kependudukan.',
                'required' => true,
            ],
            'application_form' => [
                'label' => 'Formulir Pengajuan & Persetujuan Tertulis Anggota',
                'description' => 'Konfirmasi niat pinjaman dan tanda tangan kesediaan mematuhi aturan koperasi.',
                'required' => true,
            ],
            'income_proof' => [
                'label' => 'Bukti Penghasilan / Mutasi Rekening / Slip Gaji',
                'description' => 'Verifikasi keaslian dokumen kapasitas penghasilan dan omset usaha debitur.',
                'required' => true,
            ],
            'member_status' => [
                'label' => 'Status Keanggotaan Aktif & Bebas Tunggakan',
                'description' => 'Memastikan anggota telah lunas simpanan pokok dan tidak memiliki pinjaman macet.',
                'required' => true,
            ],
            'plafon_suitability' => [
                'label' => 'Kesesuaian Plafon Pinjaman dengan Rasio Pendapatan',
                'description' => 'Angsuran bulanan tidak melebihi batas wajar kemampuan bayar (maks 40% income).',
                'required' => false,
            ],
        ];
    }
}
