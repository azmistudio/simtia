<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Academic\Entities\SchoolYear;

class Grade extends Model
{
    use HasFactory;

    protected $table = 'academic.grades';

    protected $fillable = [
        'department_id',
        'grade',
        'order',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\GradeFactory::new();
    }

    public function getGradeAttribute($value)
    {
        return Str::upper($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getSemesterByDept()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'department_id', 'department_id')->where('is_active', 1);
    }

    public function getSchoolYearByDept($id)
    {
        return SchoolYear::where('is_active',1)->where('department_id',$id)->first();
    }
}
