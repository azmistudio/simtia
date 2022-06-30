<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expenditure extends Model
{
    use HasFactory;

    protected $table = 'finance.expenditures';

    protected $fillable = [
        'department_id',
        'journal_id',
        'purpose',
        'requested_by',
        'employee_id',
        'student_id',
        'requested_id',
        'received_name',
        'requested_name',
        'trans_date',
        'total',
        'employee',
        'remark',
        'reason',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ExpenditureFactory::new();
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getJournal()
    {
        return $this->hasOne('Modules\Finance\Entities\Journal', 'id', 'journal_id');
    } 

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getRequestedUser()
    {
        return $this->hasOne('Modules\Finance\Entities\RequestedUser', 'id', 'requested_id');
    }
}
