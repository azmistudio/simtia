<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JournalVoucher extends Model
{
    use HasFactory;

    protected $table = 'finance.journal_vouchers';

    protected $fillable = [
        'department_id',
        'journal_id',
        'purpose',
        'trans_date',
        'remark',
        'reason',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\JournalVoucherFactory::new();
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getJournal()
    {
        return $this->belongsTo('Modules\Finance\Entities\Journal', 'journal_id', 'id');
    }
}
