<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReceiptCategory extends Model
{
    use HasFactory;

    protected $table = 'finance.receipt_categories';

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\ReceiptCategoryFactory::new();
    }
}
