<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavingsAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'account_no',
        'type',
        'balance',
        'status',
        'opened_at',
    ];

    protected $casts = [
        'balance' => 'integer',
        'opened_at' => 'datetime',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function transactions()
    {
        return $this->hasMany(SavingsTransaction::class);
    }

    public function gatewayPayments()
    {
        return $this->morphMany(GatewayPayment::class, 'payable')->orderBy('id', 'desc');
    }
}
