<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptVoluntary extends Model
{
    use HasFactory;

    protected $table = 'finance.receipt_voluntaries';

    protected $fillable = [
        'receipt_id',
        'journal_id',
        'prospect_student_id',
        'student_id',
        'trans_date',
        'total',
        'employee',
        'is_prospect',
        'bookyear_id',
        'department_id',
        'remark',
        'reason',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ReceiptVoluntaryFactory::new();
    }

    public function getReceipt()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptType', 'id', 'receipt_id');
    }

    public function getJournal()
    {
        return $this->belongsTo('Modules\Finance\Entities\Journal', 'journal_id', 'id');
    }

    public function getProspectiveStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\AdmissionProspect', 'id', 'prospect_student_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getBookYear()
    {
        return $this->hasOne('Modules\Finance\Entities\BookYear', 'id', 'bookyear_id');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
}
