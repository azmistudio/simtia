<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit extends Model
{
    use HasFactory;

    protected $table = 'finance.audits';

    protected $fillable = [
        'department_id',
        'bookyear_id',
        'source',
        'source_id',
        'audit_date',
        'employee',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\AuditFactory::new();
    }

    public function getBookYear()
    {
        return $this->hasOne('Modules\Finance\Entities\BookYear', 'id', 'bookyear_id');
    } 
}
