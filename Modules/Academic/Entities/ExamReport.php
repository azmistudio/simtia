<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReport extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_reports';

    protected $fillable = [
        'lesson_id',
        'class_id',
        'semester_id',
        'employee_id',
        'score_aspect_id',
        'exam_id',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamReportFactory::new();
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

    public function getScoreAspect()
    {
        return $this->hasOne('Modules\Academic\Entities\ScoreAspect', 'id', 'score_aspect_id');
    }

    public function getExam()
    {
        return $this->hasOne('Modules\Academic\Entities\Exam', 'id', 'exam_id');
    }

    public function getExamReportScoreInfo()
    {
        return $this->hasMany('Modules\Academic\Entities\ExamReportScoreInfo', 'exam_report_id', 'id');
    }

    public function getExamReportScoreFinal()
    {
        return $this->hasMany('Modules\Academic\Entities\ExamReportScoreFinal', 'exam_report_id', 'id');
    }
    
}