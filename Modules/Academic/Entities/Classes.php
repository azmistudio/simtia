<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Classes extends Model
{
    use HasFactory;

    protected $table = 'academic.classes';

    protected $fillable = [
        'grade_id',
        'schoolyear_id',
        'class',
        'employee_id',
        'capacity',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ClassesFactory::new();
    }

    public function getClassAttribute($value)
    {
        return Str::upper($value);
    }

    public function getGrade()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'id', 'grade_id');
    }

    public function getSchoolYear()
    {
        return $this->hasOne('Modules\Academic\Entities\SchoolYear', 'id', 'schoolyear_id');
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getStudents()
    {
        return $this->hasMany('Modules\Academic\Entities\Students', 'class_id', 'id');
    }
}
