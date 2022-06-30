<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JournalView extends Model
{
    use HasFactory;

    protected $table = 'finance.journals_view';

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\JournalViewFactory::new();
    }

    public function getDebitAttribute($value)
    {
        return 'Rp'.number_format($value,2);
    }

    public function getCreditAttribute($value)
    {
        return 'Rp'.number_format($value,2);
    }
}
