<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShuPeriod extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'year',
        'start_date',
        'end_date',
        'total_revenue',
        'total_expense',
        'net_shu',
        'status',
        'closed_at',
        'closed_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_revenue' => 'integer',
        'total_expense' => 'integer',
        'net_shu' => 'integer',
        'closed_at' => 'datetime',
    ];

    public function distributions()
    {
        return $this->hasMany(ShuDistribution::class);
    }

    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }
}
