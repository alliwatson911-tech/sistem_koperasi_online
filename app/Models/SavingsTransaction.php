<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'savings_account_id',
        'transaction_no',
        'type',
        'amount',
        'balance_after',
        'payment_method',
        'proof_file',
        'description',
        'created_by',
        'status',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
        'approved_at' => 'datetime',
    ];

    public function savingsAccount()
    {
        return $this->belongsTo(SavingsAccount::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
