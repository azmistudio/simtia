<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LessonScheduleInfo extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_schedule_infos';

    protected $fillable = [
        'schoolyear_id',
        'description',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonScheduleInfoFactory::new();
    }

    public function getDescriptionAttribute($value)
    {
        return Str::title($value);
    }

    public function getSchoolYear()
    {
        return $this->hasOne('Modules\Academic\Entities\SchoolYear', 'id', 'schoolyear_id');
    }

}
