<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class JournalEntry extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','entry_date','reference_type','reference_id','description','accounting_period_id','created_by', 'cash_category'];

    protected $casts = [
        'entry_date' => 'date',
    ];

    public function lines()
    {
        return $this->hasMany(JournalEntryLine::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function accountingPeriod()
    {
        return $this->belongsTo(AccountingPeriod::class);
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Adjustments created for this entry (when it was imbalanced)
     */
    public function adjustments()
    {
        return $this->hasMany(JournalEntryAdjustment::class);
    }

    /**
     * Adjustment entries created to balance other entries (where this entry is the adjustment)
     */
    public function isAdjustmentFor()
    {
        return $this->hasMany(JournalEntryAdjustment::class, 'adjustment_entry_id');
    }

    /**
     * Check if this entry has an imbalance
     */
    public function hasImbalance(): bool
    {
        $totalDebit = $this->lines()->sum('debit_amount');
        $totalCredit = $this->lines()->sum('credit_amount');
        
        return bccomp((string)$totalDebit, (string)$totalCredit, 2) !== 0;
    }

    /**
     * Get the imbalance amount (positive if more debits, negative if more credits)
     */
    public function getImbalanceAmount()
    {
        $totalDebit = $this->lines()->sum('debit_amount');
        $totalCredit = $this->lines()->sum('credit_amount');
        
        return bcsub((string)$totalDebit, (string)$totalCredit, 2);
    }

    /**
     * Get all lines including adjustment entry lines
     */
    public function getAllLines()
    {
        $lines = $this->lines;
        
        // Add adjustment lines from linked adjustment entries
        foreach ($this->adjustments as $adjustment) {
            if ($adjustment->adjustmentEntry) {
                $lines = $lines->concat($adjustment->adjustmentEntry->lines);
            }
        }
        
        return $lines;
    }

    /**
     * Get balanced total of debits including adjustments
     */
    public function getTotalDebitsIncludingAdjustments()
    {
        $total = $this->lines()->sum('debit_amount');
        
        foreach ($this->adjustments as $adjustment) {
            $total += $adjustment->debit_amount;
        }
        
        return $total;
    }

    /**
     * Get balanced total of credits including adjustments
     */
    public function getTotalCreditsIncludingAdjustments()
    {
        $total = $this->lines()->sum('credit_amount');
        
        foreach ($this->adjustments as $adjustment) {
            $total += $adjustment->credit_amount;
        }
        
        return $total;
    }
}
