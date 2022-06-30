<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $table = 'academic.teachers';

    protected $fillable = [
        'employee_id',
        'lesson_id',
        'status_id',
        'remark',
        'is_active',
        'logged'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\TeacherFactory::new();
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getLesson()
    {
        return $this->hasOne('Modules\Academic\Entities\Lesson', 'id', 'lesson_id');
    }

    public function getStatus()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'status_id');
    }

}
