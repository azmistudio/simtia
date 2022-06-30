<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Department;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\HelperTrait;
use Modules\Academic\Entities\Admission;
use Modules\Academic\Entities\AdmissionProspectGroup;
use Modules\Academic\Entities\SchoolOrigin;

class Students extends Model
{
    use HasFactory;
    use HelperTrait;
    use ReferenceTrait;

    protected $table = 'academic.students';
    protected $dates = ['dob','diploma_date','father_dob','mother_dob'];

    protected $fillable = [
        'student_no',
        'name',
        'surname',
        'year_entry',
        'prospect_student_group_id',
        'class_id',
        'tribe',
        'religion',
        'student_status',
        'economic',
        'gender',
        'pob',
        'dob',
        'citizen',
        'child_no',
        'child_brother',
        'child_status',
        'child_brother_sum',
        'child_step_sum',
        'language',
        'weight',
        'height',
        'blood',
        'photo',
        'address',
        'distance',
        'postal_code',
        'phone',
        'mobile',
        'email',
        'medical',
        'father',
        'mother',
        'father_status',
        'mother_status',
        'is_father_died',
        'is_mother_died',
        'father_pob',
        'mother_pob',
        'father_dob',
        'mother_dob',
        'father_education',
        'mother_education',
        'father_job',
        'mother_job',
        'father_income',
        'mother_income',
        'father_email',
        'mother_email',
        'parent_guardian',
        'parent_address',
        'father_mobile',
        'mother_mobile',
        'hobby',
        'mail_address',
        'remark',
        'prospect_student_id',
        'remark_admission',
        'mutation',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\StudentsFactory::new();
    }

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }

    public function getSurnameAttribute($value)
    {
        return Str::title($value);
    }

    public function getPobAttribute($value)
    {
        return Str::title($value);
    }

    public function getMedicalAttribute($value)
    {
        return $value ?: '-';
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getProspectiveGroup()
    {
        return $this->hasOne('Modules\Academic\Entities\AdmissionProspectGroup', 'id', 'prospect_student_group_id');
    }

    public function getPresenceDailyStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\PresenceDailyStudent', 'student_id', 'id');
    }

    public function getProspectiveStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\AdmissionProspect', 'student_id', 'id');
    }
}
