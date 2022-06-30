<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LessonPlan extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_plans';

    protected $fillable = [
        'department_id',
        'grade_id',
        'semester_id',
        'lesson_id',
        'code',
        'subject',
        'description',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonPlanFactory::new();
    }

    public function getCodeAttribute($value)
    {
        return Str::upper($value);
    }

    public function getSubjectAttribute($value)
    {
        return Str::title($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getGrade()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'id', 'grade_id');
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getSemester()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'id', 'semester_id');
    }
}
