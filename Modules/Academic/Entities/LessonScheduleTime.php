<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonScheduleTime extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_schedule_times';
    protected $fillable = [
        'time',
        'department_id',
        'start',
        'end',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonTimeFactory::new();
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getStartAttribute($value)
    {
        return substr($value, 0,-3);
    }

    public function getEndAttribute($value)
    {
        return substr($value, 0,-3);
    }
}
