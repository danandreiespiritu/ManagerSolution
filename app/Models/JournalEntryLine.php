<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class JournalEntryLine extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['journal_entry_id','business_id','account_id','account_code','cash_category','debit_amount','credit_amount','description'];

    protected $casts = [
        'debit_amount' => 'decimal:2',
        'credit_amount' => 'decimal:2',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function account()
    {
        return $this->belongsTo(ChartofAccounts::class, 'account_id');
    }

    public function business()
    {
        return $this->belongsTo(Business::class);
    }
}
