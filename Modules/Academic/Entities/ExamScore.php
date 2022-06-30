<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamScore extends Model
{
    use HasFactory;

    protected $table = 'academic.exam_scores';

    protected $fillable = [
        'exam_id',
        'student_id',
        'score',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ExamScoreFactory::new();
    }

    public function getScoreAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getExam()
    {
        return $this->belongsTo('Modules\Academic\Entities\Exam', 'exam_id', 'id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }
}
