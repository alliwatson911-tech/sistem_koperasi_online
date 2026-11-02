<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'gateway_payment_id',
        'loan_schedule_id',
        'principal_allocated',
        'interest_allocated',
        'penalty_allocated',
        'total_allocated',
    ];

    protected $casts = [
        'principal_allocated' => 'integer',
        'interest_allocated' => 'integer',
        'penalty_allocated' => 'integer',
        'total_allocated' => 'integer',
    ];

    public function gatewayPayment()
    {
        return $this->belongsTo(GatewayPayment::class);
    }

    public function schedule()
    {
        return $this->belongsTo(LoanSchedule::class, 'loan_schedule_id');
    }
}

