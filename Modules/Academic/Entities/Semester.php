<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Semester extends Model
{
    use HasFactory;

    protected $table = 'academic.semesters';

    protected $fillable = [
        'department_id',
        'semester',
        'remark',
        'grade_id',
        'is_active',
        'logged',
    ];

    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\SemesterFactory::new();
    }

    public function getSemesterAttribute($value)
    {
        return Str::upper($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getSchoolYearByDept()
    {
        return $this->hasOne('Modules\Academic\Entities\SchoolYear', 'department_id', 'department_id');
    }

    public function getExam()
    {
        return $this->hasOne('Modules\Academic\Entities\Exam', 'semester_id', 'id');
    }

    public function getGrade()
    {
        return $this->hasOne('Modules\Academic\Entities\Grade', 'id', 'grade_id');
    }
}
