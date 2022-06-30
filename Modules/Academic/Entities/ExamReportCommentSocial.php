<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamReportCommentSocial extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_report_comment_socials';

    protected $fillable = [
        'lesson_id',
        'type_id',
        'grade_id',
        'aspect',
        'comment',
        'is_active',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamReportCommentSocialFactory::new();
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getGrade()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'id', 'grade_id');
    }

    public function getType()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'type_id');
    }
}
