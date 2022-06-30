<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonScheduleTeaching extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_schedule_teachings';

    protected $fillable = [
        'class_id',
        'employee_id',
        'department_id',
        'schedule_id',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonScheduleTeachingFactory::new();
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getScheduleInfo()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonScheduleInfo', 'id', 'schedule_id');
    }
    
    public function getGradeByDept()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'department_id', 'department_id');
    }
}
