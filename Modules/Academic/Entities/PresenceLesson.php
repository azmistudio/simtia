<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PresenceLesson extends Model
{
    use HasFactory;

    protected $table = 'academic.presence_lessons';

    protected $fillable = [
        'class_id',
        'semester_id',
        'lesson_id',
        'date',
        'time',
        'employee_id',
        'remark',
        'subject',
        'objective',
        'reflection',
        'plan',
        'late',
        'times',
        'teacher_type',
        'lesson_schedule_id',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\PresenceLessonFactory::new();
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getSemester()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'id', 'semester_id');
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getLessonSchedule()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonSchedule', 'id', 'lesson_schedule_id');
    }

    public function getTeacherType()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'teacher_type');
    }
}
