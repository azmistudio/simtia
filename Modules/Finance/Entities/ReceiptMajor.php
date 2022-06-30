<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptMajor extends Model
{
    use HasFactory;

    protected $table = 'finance.receipt_majors';

    protected $fillable = [
        'major_id',
        'journal_id',
        'trans_date',
        'total',
        'employee_id',
        'remark',
        'reason',
        'is_prospect',
        'first_instalment',
        'discount_amount',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ReceiptMajorFactory::new();
    }

    public function getPaymentMajor()
    {
        return $this->belongsTo('Modules\Finance\Entities\PaymentMajor', 'major_id', 'id');
    }

    public function getJournal()
    {
        return $this->belongsTo('Modules\Finance\Entities\Journal', 'journal_id', 'id');
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }
}
