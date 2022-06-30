<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Lesson extends Model
{
    use HasFactory;

    protected $table = 'academic.lessons';

    protected $fillable = [
        'code',
        'name',
        'department_id',
        'group_id',
        'mandatory',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonFactory::new();
    }

    public function getCodeAttribute($value)
    {
        return Str::upper($value);
    }

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getLessonGroup()
    {
        return $this->hasOne('Modules\Academic\Entities\LessonGroup', 'id', 'group_id');
    }

    public function getTeacher()
    {
        return $this->hasOne('Modules\Academic\Entities\Teacher', 'lesson_id', 'id');
    }
}
