<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Http\Traits\ReferenceTrait;
use App\Models\Reference;
use Modules\HR\Entities\Employee;
use Modules\Academic\Entities\AcademicClass;
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Entities\LessonTime;

class LessonSchedule extends Model
{
    use HasFactory;
    use ReferenceTrait;

    protected $table = 'academic.lesson_schedules';

    protected $fillable = [
        'class_id',
        'employee_id',
        'department_id',
        'schedule_id',
        'teaching_id',
        'lesson_id',
        'day',
        'from_time',
        'to_time',
        'feature',
        'teaching_status',
        'remark',
        'start',
        'end',
        'time_id_1',
        'time_id_2',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonScheduleFactory::new();
    }

    public function getClasses()
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

    public function getScheduleTeaching()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonScheduleTeaching', 'id', 'teaching_id');
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getTeachingStatus()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'teaching_status');
    }

    public function getTimeId1()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonScheduleTime', 'id', 'time_id_1');
    }

    public function getTimeId2()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonScheduleTime', 'id', 'time_id_2');
    }

}
