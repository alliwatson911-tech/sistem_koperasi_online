<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanSchedule extends Model
{
    use HasFactory;

    public const STATUS_UNPAID = 'unpaid';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';

    protected $fillable = [
        'loan_id',
        'installment_no',
        'due_date',
        'principal_amount',
        'interest_amount',
        'total_amount',
        'paid_principal',
        'paid_interest',
        'paid_penalty',
        'remaining_principal',
        'status',
        'paid_at',
        'days_overdue',
        'penalty_amount',
    ];

    protected $casts = [
        'due_date' => 'date',
        'principal_amount' => 'integer',
        'interest_amount' => 'integer',
        'total_amount' => 'integer',
        'paid_principal' => 'integer',
        'paid_interest' => 'integer',
        'paid_penalty' => 'integer',
        'remaining_principal' => 'integer',
        'paid_at' => 'datetime',
        'penalty_amount' => 'integer',
        'days_overdue' => 'integer',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function penalties()
    {
        return $this->hasMany(Penalty::class);
    }

    public function getPeriodLabelAttribute(): string
    {
        $isWeekly = false;
        if ($this->relationLoaded('loan') && $this->loan) {
            $isWeekly = $this->loan->isWeekly();
        }
        $unitLabel = $isWeekly ? 'Minggu' : 'Bulan';
        return "{$unitLabel} {$this->installment_no}";
    }
}
