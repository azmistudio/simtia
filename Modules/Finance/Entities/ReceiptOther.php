<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptOther extends Model
{
    use HasFactory;

    protected $table = 'finance.receipt_others';

    protected $fillable = [
        'receipt_id',
        'journal_id',
        'trans_date',
        'total',
        'source',
        'employee',
        'bookyear_id',
        'department_id',
        'remark',
        'reason',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ReceiptOtherFactory::new();
    }

    public function getReceipt()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptType', 'id', 'receipt_id');
    }

    public function getJournal()
    {
        return $this->belongsTo('Modules\Finance\Entities\Journal', 'journal_id', 'id');
    }

    public function getBookYear()
    {
        return $this->hasOne('Modules\Finance\Entities\BookYear', 'id', 'bookyear_id');
    }
}
