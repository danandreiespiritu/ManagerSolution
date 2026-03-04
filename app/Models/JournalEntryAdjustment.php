<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class JournalEntryAdjustment extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = [
        'journal_entry_id',
        'adjustment_entry_id',
        'business_id',
        'account_id',
        'debit_amount',
        'credit_amount',
        'adjustment_type',
        'reason',
        'is_applied',
    ];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'is_applied' => 'boolean',
    ];

    /**
     * The original journal entry that required adjustment
     */
    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    /**
     * The adjustment entry created to balance the original entry
     */
    public function adjustmentEntry()
    {
        return $this->belongsTo(JournalEntry::class, 'adjustment_entry_id');
    }

    /**
     * The account used for the adjustment
     */
    public function account()
    {
        return $this->belongsTo(ChartofAccounts::class, 'account_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope to get adjustments that haven't been applied yet
     */
    public function scopeUnapplied($query)
    {
        return $query->where('is_applied', false);
    }

    /**
     * Scope to get adjustments that have been applied
     */
    public function scopeApplied($query)
    {
        return $query->where('is_applied', true);
    }

    /**
     * Get the adjustment amount (absolute value)
     */
    public function getAdjustmentAmountAttribute()
    {
        return max($this->debit_amount, $this->credit_amount);
    }
}
