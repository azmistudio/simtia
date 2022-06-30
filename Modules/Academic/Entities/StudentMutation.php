<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use App\Http\Traits\HelperTrait;
use App\Models\Department;
use App\Models\Reference;
use Modules\Academic\Entities\Students;

class StudentMutation extends Model
{
    use HasFactory;
    use HelperTrait;

    protected $table = 'academic.student_mutations';

    protected $fillable = [
        'student_id',
        'mutation_id',
        'department_id',
        'mutation_date',
        'remark',
        'logged',
    ];

    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\StudentMutationFactory::new();
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getMutation()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'mutation_id');
    }

    public function getAlumniByStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\StudentAlumni', 'student_id', 'student_id');
    }
}
