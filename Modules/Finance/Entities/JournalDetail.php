<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JournalDetail extends Model
{
    use HasFactory;

    protected $table = 'finance.journal_details';

    protected $fillable = [
        'journal_id',
        'account_id',
        'debit',
        'credit',
        'uuid',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\JournalDetailFactory::new();
    }

    public function getJournal()
    {
        return $this->belongsTo('Modules\Finance\Entities\Journal', 'journal_id', 'id');
    }

    public function getAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'account_id');
    }
}
