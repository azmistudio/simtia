<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AdmissionProspect extends Model
{
    use HasFactory;

    protected $table = 'academic.prospect_students';
    protected $dates = ['dob','diploma_date','father_dob','mother_dob'];

    protected $fillable = [
        'registration_no',
        'student_no',
        'name',
        'surname',
        'year_entry',
        'prospect_group_id',
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
        'donation_1',
        'donation_2',
        'exam_01',
        'exam_02',
        'exam_03',
        'exam_04',
        'exam_05',
        'exam_06',
        'exam_07',
        'exam_08',
        'exam_09',
        'exam_10',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AdmissionProspectFactory::new();
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

    public function getProspectGroup()
    {
        return $this->hasOne('Modules\Academic\Entities\AdmissionProspectGroup', 'id', 'prospect_group_id');
    }

    public function getStudent()
    {
        return $this->belongsTo('Modules\Academic\Entities\Students', 'student_id', 'id');
    }
}
