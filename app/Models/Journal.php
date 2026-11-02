<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Journal extends Model
{
    use HasFactory;

    public const STATUS_POSTED = 'posted';
    public const STATUS_REVERSED = 'reversed';

    protected $fillable = [
        'journal_no',
        'date',
        'reference_type',
        'reference_id',
        'description',
        'total_amount',
        'status',
        'reversed_by_id',
        'created_by',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'integer',
    ];

    public function entries()
    {
        return $this->hasMany(JournalEntry::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reverser()
    {
        return $this->belongsTo(User::class, 'reversed_by_id');
    }

    public function getTotalDebitAttribute(): int
    {
        return (int) $this->entries()->where('type', 'debit')->sum('amount');
    }

    public function getTotalCreditAttribute(): int
    {
        return (int) $this->entries()->where('type', 'credit')->sum('amount');
    }

    public function isBalanced(): bool
    {
        $debit = (int) $this->entries()->where('type', 'debit')->sum('amount');
        $credit = (int) $this->entries()->where('type', 'credit')->sum('amount');
        return $debit === $credit;
    }
}
