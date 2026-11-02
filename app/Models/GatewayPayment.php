<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewayPayment extends Model
{
    use HasFactory;

    public const STATUS_PENDING   = 'pending';
    public const STATUS_PAID      = 'paid';
    public const STATUS_FAILED    = 'failed';
    public const STATUS_EXPIRED   = 'expired';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED  = 'refunded';

    public const TYPE_LOAN_INSTALLMENT = 'loan_installment';
    public const TYPE_SAVINGS_DEPOSIT  = 'savings_deposit';

    protected $fillable = [
        'order_id',
        'member_id',
        'transaction_type',
        'payable_type',
        'payable_id',
        'amount',
        'fee_amount',
        'fee_bearer',
        'gross_amount',
        'principal_portion',
        'interest_portion',
        'penalty_portion',
        'overpayment_portion',
        'gateway',
        'payment_method_type',
        'va_number',
        'qr_string',
        'qr_url',
        'payment_url',
        'status',
        'expires_at',
        'paid_at',
        'settled_at',
        'settlement_id',
        'loan_payment_id',
        'savings_transaction_id',
        'journal_id',
        'selected_schedule_ids',
        'raw_payload',
        'raw_response',
        'notes',
    ];

    protected $casts = [
        'amount' => 'integer',
        'fee_amount' => 'integer',
        'gross_amount' => 'integer',
        'principal_portion' => 'integer',
        'interest_portion' => 'integer',
        'penalty_portion' => 'integer',
        'overpayment_portion' => 'integer',
        'selected_schedule_ids' => 'array',
        'raw_payload' => 'array',
        'raw_response' => 'array',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'settled_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function settlement()
    {
        return $this->belongsTo(GatewaySettlement::class, 'settlement_id');
    }

    public function loanPayment()
    {
        return $this->belongsTo(LoanPayment::class);
    }

    public function savingsTransaction()
    {
        return $this->belongsTo(SavingsTransaction::class);
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function isPaid(): bool
    {
        return $this->status === self::STATUS_PAID;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED || ($this->isPending() && $this->expires_at->isPast());
    }

    public function getMethodLabelAttribute(): string
    {
        return match ($this->payment_method_type) {
            'qris' => 'QRIS (Semua Bank / Dompet Digital)',
            'va_bca' => 'BCA Virtual Account',
            'va_bni' => 'BNI Virtual Account',
            'va_bri' => 'BRI Virtual Account',
            'va_mandiri' => 'Mandiri Bill / VA',
            'va_permata' => 'Permata Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            'cstore' => 'Minimarket (Indomaret / Alfamart)',
            default => strtoupper(str_replace('_', ' ', $this->payment_method_type)),
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PAID => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Lunas (Paid)</span>',
            self::STATUS_PENDING => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800">Menunggu Pembayaran</span>',
            self::STATUS_EXPIRED => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Kedaluwarsa (Expired)</span>',
            self::STATUS_FAILED => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">Gagal (Failed)</span>',
            self::STATUS_CANCELLED => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">Dibatalkan</span>',
            self::STATUS_REFUNDED => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">Dikembalikan (Refunded)</span>',
            default => '<span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">' . ucfirst($this->status) . '</span>',
        };
    }
}

