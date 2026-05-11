<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FinancialTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'transaction_date'   => 'date',
        'amount'             => 'decimal:2',
        'dpp_amount'         => 'decimal:2',
        'tax_amount'         => 'decimal:2',
        'running_balance'    => 'decimal:2',
        'is_end_of_month'    => 'boolean',
        'is_end_of_year'     => 'boolean',
        'pph_transaction_id' => 'integer',
    ];

    // ── Jenis pajak yang bersifat pemotongan (withholding) ──────────
    const PPH_TYPES = ['pph_21', 'pph_23', 'pph_4_ayat_2'];

    // ── Label PPh untuk deskripsi auto-transaksi ────────────────────
    const PPH_LABELS = [
        'pph_21'       => 'PPh 21',
        'pph_23'       => 'PPh 23',
        'pph_4_ayat_2' => 'PPh 4 Ayat 2',
    ];

    // ── Prefix untuk transaksi PPh yang dibuat otomatis ─────────────
    const AUTO_PPH_PREFIX = '[AUTO PPh]';

    public function senderEntity()
    {
        return $this->belongsTo(FinancialEntity::class, 'sender_entity_id');
    }

    public function receiverEntity()
    {
        return $this->belongsTo(FinancialEntity::class, 'receiver_entity_id');
    }

    public function account()
    {
        return $this->belongsTo(FinancialAccount::class, 'account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Transaksi kredit PPh yang dibuat otomatis dari transaksi ini.
     */
    public function pphTransaction()
    {
        return $this->belongsTo(FinancialTransaction::class, 'pph_transaction_id');
    }

    /**
     * Apakah transaksi ini adalah PPh yang dibuat otomatis oleh sistem?
     */
    public function isAutoPph(): bool
    {
        return str_starts_with($this->description ?? '', self::AUTO_PPH_PREFIX);
    }

    /**
     * Apakah transaksi ini memiliki PPh yang perlu diproses?
     */
    public function hasPphTax(): bool
    {
        return in_array($this->tax_type, self::PPH_TYPES)
            && ($this->tax_amount ?? 0) > 0;
    }
}
