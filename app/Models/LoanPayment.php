<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'payment_no',
        'payment_date',
        'amount',
        'principal_portion',
        'interest_portion',
        'penalty_portion',
        'payment_method',
        'proof_file',
        'status',
        'verified_by',
        'gateway_payment_id',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'integer',
        'principal_portion' => 'integer',
        'interest_portion' => 'integer',
        'penalty_portion' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function gatewayPayment()
    {
        return $this->belongsTo(GatewayPayment::class, 'gateway_payment_id');
    }
}
