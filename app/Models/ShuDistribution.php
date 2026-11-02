<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuDistribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'shu_period_id',
        'member_id',
        'member_savings_total',
        'member_loan_interest_total',
        'savings_portion_amount',
        'transaction_portion_amount',
        'total_shu_amount',
        'status',
        'distributed_at',
    ];

    protected $casts = [
        'member_savings_total' => 'integer',
        'member_loan_interest_total' => 'integer',
        'savings_portion_amount' => 'integer',
        'transaction_portion_amount' => 'integer',
        'total_shu_amount' => 'integer',
        'distributed_at' => 'datetime',
    ];

    public function shuPeriod()
    {
        return $this->belongsTo(ShuPeriod::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
