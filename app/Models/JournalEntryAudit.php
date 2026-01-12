<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToBusiness;

class JournalEntryAudit extends Model
{
    use HasFactory;
    use BelongsToBusiness;

    protected $fillable = ['journal_entry_id','user_id','business_id','action','details'];

    protected $casts = [
        'details' => 'array',
    ];

    public function journalEntry()
    {
        return $this->belongsTo(JournalEntry::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
