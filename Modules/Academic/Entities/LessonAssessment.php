<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LessonAssessment extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_assessments';

    protected $fillable = [
        'employee_id',
        'grade_id',
        'lesson_id',
        'score_aspect_id',
        'exam_id',
        'value',
        'logged'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonAssesmentFactory::new();
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getGrade()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'id', 'grade_id');
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getScoreAspect()
    {
        return $this->hasOne('Modules\Academic\Entities\ScoreAspect', 'id', 'score_aspect_id');
    }

    public function getLessonExam()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonExam', 'id', 'exam_id');
    }

    public function getTeacherStatus($lesson_id)
    {
        $instance = $this->hasOne('Modules\Academic\Entities\Teacher', 'employee_id', 'employee_id');
        return $instance->where('lesson_id', $lesson_id);
    }
}
