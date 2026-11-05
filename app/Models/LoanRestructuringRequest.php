<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRestructuringRequest extends Model
{
    use HasFactory;

    public const TYPE_PENALTY_WAIVER = 'penalty_waiver';
    public const TYPE_TENOR_EXTENSION = 'tenor_extension';
    public const TYPE_PAYMENT_RESCHEDULE = 'payment_reschedule';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'loan_id',
        'requested_by_user_id',
        'approved_by_user_id',
        'request_type',
        'requested_waiver_amount',
        'approved_waiver_amount',
        'reason',
        'manager_notes',
        'status',
        'decided_at',
    ];

    protected $casts = [
        'requested_waiver_amount' => 'integer',
        'approved_waiver_amount' => 'integer',
        'decided_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function requester()
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function decider()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->request_type) {
            self::TYPE_PENALTY_WAIVER => 'Penghapusan / Pembebasan Denda',
            self::TYPE_TENOR_EXTENSION => 'Perpanjangan Masa Tenor Angsuran',
            self::TYPE_PAYMENT_RESCHEDULE => 'Penjadwalan Ulang (Rescheduling)',
            default => ucfirst(str_replace('_', ' ', $this->request_type)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_APPROVED => 'badge-success',
            self::STATUS_REJECTED => 'badge-danger',
            default => 'badge-gray',
        };
    }
}
