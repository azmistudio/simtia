<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LessonExam extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_exams';

    protected $fillable = [
        'lesson_id',
        'score_aspect_id',
        'code',
        'subject',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonExamFactory::new();
    }

    public function getCodeAttribute($value)
    {
        return Str::upper($value);
    }

    public function getSubjectAttribute($value)
    {
        return Str::upper($value);
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getScoreAspect()
    {
        return $this->hasOne('Modules\Academic\Entities\ScoreAspect', 'id', 'score_aspect_id');
    }
}
