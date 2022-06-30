<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptType extends Model
{
    use HasFactory;

    protected $table = 'finance.receipt_types';

    protected $fillable = [
        'name',
        'amount',
        'category_id',
        'cash_account',
        'receipt_account',
        'receivable_account',
        'discount_account',
        'department_id',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ReceiptTypeFactory::new();
    }

    public function getCategory()
    {
        return $this->hasOne('Modules\Finance\Entities\ReceiptCategory', 'id', 'category_id');
    }

    public function getCashAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'cash_account');
    }

    public function getReceiptAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'receipt_account');
    }

    public function getReceivableAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'receivable_account');
    }

    public function getDiscountAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'discount_account');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
}
