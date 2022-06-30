<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReportScoreInfo extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_report_score_infos';

    protected $fillable = [
        'exam_report_id',
        'lesson_id',
        'class_id',
        'semester_id',
        'value',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamReportScoreInfoFactory::new();
    }

    public function getExamReport()
    {
        return $this->hasOne('Modules\Academic\Entities\ExamReport', 'id', 'exam_report_id');
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
}
