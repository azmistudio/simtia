<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Saving extends Model
{
    use HasFactory;

    protected $table = 'finance.savings';

    protected $fillable = [
        'employee_id',
        'student_id',
        'is_employee',
        'saving_id',
        'journal_id',
        'trans_date',
        'debit',
        'credit',
        'employee',
        'remark',
        'reason',
        'bookyear_id',
        'transaction_type',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\SavingFactory::new();
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getSavingType()
    {
        return $this->hasOne('Modules\Finance\Entities\SavingType', 'id', 'saving_id');
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
