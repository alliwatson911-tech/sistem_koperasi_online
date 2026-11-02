<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_VERIFIED = 'verified';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_SUSPENDED = 'suspended';
    public const STATUS_RESIGNED = 'resigned';

    protected $fillable = [
        'user_id',
        'member_no',
        'nik',
        'birth_date',
        'birth_place',
        'gender',
        'address',
        'occupation',
        'monthly_income',
        'bank_name',
        'bank_account',
        'status',
        'verified_at',
        'verified_by',
        'activated_at',
        'activated_by',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'verified_at' => 'datetime',
        'activated_at' => 'datetime',
        'monthly_income' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function activator()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    public function documents()
    {
        return $this->hasMany(MemberDocument::class);
    }

    public function savingsAccounts()
    {
        return $this->hasMany(SavingsAccount::class);
    }

    public function loanApplications()
    {
        return $this->hasMany(LoanApplication::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function gatewayPayments()
    {
        return $this->hasMany(GatewayPayment::class)->orderBy('id', 'desc');
    }

    public function shuDistributions()
    {
        return $this->hasMany(ShuDistribution::class);
    }

    public function getSimpananPokokAccount()
    {
        return $this->savingsAccounts()->where('type', 'pokok')->first();
    }

    public function getSimpananWajibAccount()
    {
        return $this->savingsAccounts()->where('type', 'wajib')->first();
    }

    public function getSimpananSukarelaAccount()
    {
        return $this->savingsAccounts()->where('type', 'sukarela')->first();
    }

    public function getTotalSavingsAttribute(): int
    {
        return (int) $this->savingsAccounts()->sum('balance');
    }
}
