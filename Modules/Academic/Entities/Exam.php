<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $table = 'academic.exams';

    protected $fillable = [
        'teacher_id',
        'lesson_id',
        'class_id',
        'semester_id',
        'employee_id',
        'status_id',
        'score_aspect_id',
        'lesson_exam_id',
        'description',
        'date',
        'lesson_assessment_id',
        'lesson_plan_id',
        'code',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamFactory::new();
    }

    public function getTeacher()
    {
        return $this->hasOne('Modules\Academic\Entities\Teacher', 'id', 'teacher_id');
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getSemester()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'id', 'semester_id');
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getStatus()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'status_id');
    }

    public function getScoreAspect()
    {
        return $this->hasOne('Modules\Academic\Entities\ScoreAspect', 'id', 'score_aspect_id');
    }

    public function getLessonExam()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonExam', 'id', 'lesson_exam_id');
    }

    public function getLessonAssessment()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonAssessment', 'id', 'lesson_assessment_id');
    }

    public function getExamScore()
    {
        return $this->hasOne('Modules\Academic\Entities\ExamScore', 'exam_id', 'id');
    }
}
