<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReportCommentLesson extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_report_comment_lessons';

    protected $fillable = [
        'lesson_id',
        'score_aspect_id',
        'grade_id',
        'comment',
        'is_active',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamReportCommentLessonFactory::new();
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getScoreAspect()
    {
        return $this->hasOne('Modules\Academic\Entities\ScoresAspect', 'id', 'score_aspect_id');
    }

    public function getGrade()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'id', 'grade_id');
    }
}
