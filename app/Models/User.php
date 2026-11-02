<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_MANAGER = 'manager';
    public const ROLE_ANALYST = 'analyst';
    public const ROLE_TELLER = 'teller';
    public const ROLE_MEMBER = 'member';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'role',
        'employee_code',
        'is_active',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function loanApplicationsAssigned()
    {
        return $this->hasMany(LoanApplication::class, 'assigned_to');
    }

    public function loanApplicationsReceived()
    {
        return $this->hasMany(LoanApplication::class, 'received_by');
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isManager(): bool
    {
        return $this->role === self::ROLE_MANAGER;
    }

    public function isAnalyst(): bool
    {
        return $this->role === self::ROLE_ANALYST;
    }

    public function isTeller(): bool
    {
        return $this->role === self::ROLE_TELLER;
    }

    public function isMember(): bool
    {
        return $this->role === self::ROLE_MEMBER;
    }

    public function isStaff(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_ANALYST, self::ROLE_TELLER]);
    }

    public function getEmployeeCodeFormattedAttribute(): string
    {
        if (!empty($this->employee_code)) {
            return $this->employee_code;
        }

        $prefix = match ($this->role) {
            self::ROLE_TELLER => 'TLR',
            self::ROLE_ANALYST => 'ANL',
            self::ROLE_MANAGER => 'MGR',
            self::ROLE_ADMIN => 'ADM',
            default => 'USR',
        };

        return sprintf('%s-%03d', $prefix, $this->id);
    }

    public function getDisplayNameWithCodeAttribute(): string
    {
        return "{$this->name} ({$this->employee_code_formatted})";
    }
}
