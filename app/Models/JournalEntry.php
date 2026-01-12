<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class JournalEntry extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['user_id','business_id','entry_date','reference_type','reference_id','description','accounting_period_id','created_by'];

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
}
