<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SavingType extends Model
{
    use HasFactory;

    protected $table = 'finance.saving_types';

    protected $fillable = [
        'is_employee',
        'name',
        'cash_account',
        'credit_account',
        'department_id',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\SavingTypeFactory::new();
    }

    public function getCashAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'cash_account');
    }

    public function getCreditAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'credit_account');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
}
