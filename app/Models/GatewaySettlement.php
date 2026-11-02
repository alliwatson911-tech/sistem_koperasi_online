<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewaySettlement extends Model
{
    use HasFactory;

    protected $fillable = [
        'settlement_no',
        'gateway',
        'total_transactions',
        'gross_amount',
        'fee_amount',
        'net_amount',
        'bank_account_code',
        'journal_id',
        'status',
        'settled_at',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'total_transactions' => 'integer',
        'gross_amount' => 'integer',
        'fee_amount' => 'integer',
        'net_amount' => 'integer',
        'settled_at' => 'datetime',
    ];

    public function payments()
    {
        return $this->hasMany(GatewayPayment::class, 'settlement_id');
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

