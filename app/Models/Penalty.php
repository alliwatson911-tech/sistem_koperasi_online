<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'loan_schedule_id',
        'calculated_date',
        'days_overdue',
        'daily_rate',
        'amount',
        'is_settled',
        'settled_at',
    ];

    protected $casts = [
        'calculated_date' => 'date',
        'daily_rate' => 'decimal:4',
        'amount' => 'integer',
        'is_settled' => 'boolean',
        'settled_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function loanSchedule()
    {
        return $this->belongsTo(LoanSchedule::class);
    }
}
