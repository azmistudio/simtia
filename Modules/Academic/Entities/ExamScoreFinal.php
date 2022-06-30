<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamScoreFinal extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_score_finals';

    protected $fillable = [
        'lesson_id',
        'student_id',
        'class_id',
        'semester_id',
        'lesson_exam_id',
        'lesson_assessment_id',
        'score',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamScoreFinalFactory::new();
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getSemester()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'id', 'semester_id');
    }

    public function getLessonExam()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonExam', 'id', 'lesson_exam_id');
    }

    public function getLessonAssesment()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonAssesment', 'id', 'lesson_assesment_id');
    }
}
