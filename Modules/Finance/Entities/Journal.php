<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Journal extends Model
{
    use HasFactory;

    protected $table = 'finance.journals';

    protected $fillable = [
        'journal_date',
        'transaction',
        'cash_no',
        'employee_id',
        'bookyear_id',
        'source',
        'department_id',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\JournalFactory::new();
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getBookYear()
    {
        return $this->hasOne('Modules\Finance\Entities\BookYear', 'id', 'bookyear_id');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getJournalDetails()
    {
        return $this->hasMany('Modules\Finance\Entities\JournalDetail', 'journal_id', 'id');
    }

    public function getJournalVoucher()
    {
        return $this->hasOne('Modules\Finance\Entities\JournalVoucher', 'journal_id', 'id');
    }

    public function getPaymentMajor()
    {
        return $this->hasOne('Modules\Finance\Entities\PaymentMajor', 'journal_id', 'id');
    }

    public function getReceiptMajor()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptMajor', 'journal_id', 'id');
    }

    public function getReceiptOther()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptOther', 'journal_id', 'id');
    }

    public function getReceiptVoluntary()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptVoluntary', 'journal_id', 'id');
    }

    public function getSaving()
    {
        return $this->hasOne('Modules\Finance\Entities\Saving', 'journal_id', 'id');
    }
}
