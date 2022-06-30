<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class PresenceLessonStudent extends Model
{
    use HasFactory;

    protected $table = 'academic.presence_lesson_students';

    protected $fillable = [
        'presence_id',
        'student_id',
        'presence',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\PresenceLessonStudentFactory::new();
    }

    public function getPresenceLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\PresenceLesson', 'id', 'presence_id');
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }
}
