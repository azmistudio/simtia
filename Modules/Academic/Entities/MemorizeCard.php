<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MemorizeCard extends Model
{
    use HasFactory;

    protected $table = 'academic.memorize_cards';
    protected $fillable = [
        'class_id',
        'student_id',
        'employee_id',
        'memorize_date',
        'from_surah_id',
        'to_surah_id',
        'from_verse',
        'to_verse',
        'status',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\MemorizeCardFactory::new();
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

}
