<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PresenceDailyStudent extends Model
{
    use HasFactory;

    protected $table = 'academic.presence_daily_students';

    protected $fillable = [
        'presence_id',
        'student_id',
        'present',
        'permit',
        'sick',
        'absent',
        'leave',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\PresenceDailyStudentFactory::new();
    }

    public function getPresenceDaily()
    {
        return $this->belongsTo('Modules\Academic\Entities\PresenceDaily', 'presence_id', 'id');
    }

    public function getStudent()
    {
        return $this->belongsTo('Modules\Academic\Entities\Students', 'student_id', 'id');
    }
}
