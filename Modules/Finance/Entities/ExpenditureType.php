<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExpenditureType extends Model
{
    use HasFactory;

    protected $table = 'finance.expenditure_types';

    protected $fillable = [
        'department_id',
        'name',
        'amount',
        'debit_account',
        'credit_account',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ExpenditureTypeFactory::new();
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getDebitAccount()
    {
        return $this->hasOne('App\Models\Department', 'id', 'debit_account');
    }

    public function getCreditAccount()
    {
        return $this->hasOne('App\Models\Department', 'id', 'credit_account');
    }
}
