<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Code extends Model
{
    use HasFactory;

    protected $table = 'finance.codes';

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'remark',
        'balance',
        'parent',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\CodeFactory::new();
    }

    public function getBalanceAttribute($value)
    {
        return number_format($value,2,',','.');
    }

    public function getCategory()
    {
        return $this->hasOne('Modules\Finance\Entities\CodeCategory', 'id', 'category_id');
    }  
}
