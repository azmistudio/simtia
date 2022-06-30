<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BeginBalance extends Model
{
    use HasFactory;

    protected $table = 'finance.begin_balances';

    protected $fillable = [
        'bookyear_id',
        'trans_date',
        'account_id',
        'total',
        'pos',
        'reason',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\BeginBalanceFactory::new();
    }

    public function getBookYear()
    {
        return $this->hasOne('Modules\Finance\Entities\BookYear', 'id', 'bookyear_id');
    }

    public function getAccount()
    {
        return $this->hasOne('Modules\Finance\Entities\Code', 'id', 'account_id');
    }
}
