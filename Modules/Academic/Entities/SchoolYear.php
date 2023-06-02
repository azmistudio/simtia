<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class SchoolYear extends Model
{
    use HasFactory;

    protected $table = 'academic.schoolyears';
    protected $dates = ['start_date','end_date'];

    protected $fillable = [
        'department_id',
        'school_year',
        'start_date',
        'end_date',
        'remark',
        'is_active',
        'logged',
    ];

    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\SchoolYearFactory::new();
    }

    public function getSchoolYearAttribute($value)
    {
        return Str::upper($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
}
