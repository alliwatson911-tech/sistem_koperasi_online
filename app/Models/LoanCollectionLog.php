<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanCollectionLog extends Model
{
    use HasFactory;

    public const ACTION_PHONE = 'phone';
    public const ACTION_WHATSAPP = 'whatsapp';
    public const ACTION_FIELD_VISIT = 'field_visit';
    public const ACTION_LETTER_SP1 = 'letter_sp1';
    public const ACTION_LETTER_SP2 = 'letter_sp2';
    public const ACTION_LETTER_SP3 = 'letter_sp3';
    public const ACTION_MEDIATION = 'mediation';
    public const ACTION_PROMISE_TO_PAY = 'promise_to_pay';

    public const RESULT_PROMISE_TO_PAY = 'promise_to_pay';
    public const RESULT_PARTIAL_PAID = 'partial_paid';
    public const RESULT_UNREACHABLE = 'unreachable';
    public const RESULT_UNCOOPERATIVE = 'uncooperative';
    public const RESULT_DISPUTE = 'dispute';
    public const RESULT_COOPERATIVE = 'cooperative';

    protected $fillable = [
        'loan_id',
        'user_id',
        'action_type',
        'action_date',
        'result_status',
        'promised_date',
        'promised_amount',
        'notes',
        'proof_photo_path',
    ];

    protected $casts = [
        'action_date' => 'datetime',
        'promised_date' => 'date',
        'promised_amount' => 'integer',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getActionTypeLabelAttribute(): string
    {
        return $this->action_label;
    }

    public function getResultStatusLabelAttribute(): string
    {
        return $this->result_label;
    }

    public function getActionLabelAttribute(): string
    {
        return match ($this->action_type) {
            self::ACTION_PHONE => 'Panggilan Telepon',
            self::ACTION_WHATSAPP => 'Pesan WhatsApp Penagihan',
            self::ACTION_FIELD_VISIT => 'Kunjungan Lapangan / Penagihan Fisik',
            self::ACTION_LETTER_SP1 => 'Surat Peringatan 1 (SP-1)',
            self::ACTION_LETTER_SP2 => 'Surat Peringatan 2 (SP-2)',
            self::ACTION_LETTER_SP3 => 'Surat Peringatan 3 / Somasi (SP-3)',
            self::ACTION_MEDIATION => 'Mediasi Kantor Koperasi',
            self::ACTION_PROMISE_TO_PAY => 'Komitmen / Janji Bayar Anggota',
            default => ucfirst(str_replace('_', ' ', $this->action_type)),
        };
    }

    public function getResultLabelAttribute(): string
    {
        return match ($this->result_status) {
            self::RESULT_PROMISE_TO_PAY => 'Janji Bayar',
            self::RESULT_PARTIAL_PAID => 'Bayar Sebagian',
            self::RESULT_UNREACHABLE => 'Tidak Dapat Dihubungi / Rumah Kosong',
            self::RESULT_UNCOOPERATIVE => 'Menolak Bayar / Tidak Kooperatif',
            self::RESULT_DISPUTE => 'Sengketa / Keberatan Perhitungan',
            self::RESULT_COOPERATIVE => 'Kooperatif / Memohon Keringanan',
            default => ucfirst(str_replace('_', ' ', $this->result_status)),
        };
    }
}
