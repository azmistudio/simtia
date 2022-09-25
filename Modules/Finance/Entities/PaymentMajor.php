<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMajor extends Model
{
    use HasFactory;

    protected $table = 'finance.payment_majors';

    protected $fillable = [
        'department_id',
        'category_id',
        'prospect_student_id',
        'student_id',
        'receipt_id',
        'amount',
        'instalment',
        'remark',
        'reason',
        'is_paid',
        'is_prospect',
        'journal_id',
        'bookyear_id',
        'logged',
        'period_month',
        'period_year'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\PaymentMajorFactory::new();
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getCategory()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptCategory', 'id', 'category_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getProspectStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\ProspectStudentView', 'id', 'prospect_student_id');
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

    public function getReceiptMajor()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptMajor', 'major_id', 'id');
    }
}
