<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReportScoreFinal extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_report_score_finals';

    protected $fillable = [
        'exam_report_id',
        'student_id',
        'lesson_assessment_id',
        'exam_report_info_id',
        'value',
        'value_letter',
        'comment',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamReportScoreFinalFactory::new();
    }

    public function getExamReport()
    {
        return $this->hasOne('Modules\Academic\Entities\ExamReport', 'id', 'exam_report_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getLessonAssesment()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonAssesment', 'id', 'lesson_assessment_id');
    }

    public function getExamReportScoreInfo()
    {
        return $this->hasOne('Modules\Academic\Entities\ExamReportScoreInfo', 'id', 'exam_report_info_id');
    }
}
