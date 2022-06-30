<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class AdmissionProspectGroup extends Model
{
    use HasFactory;

    protected $table = 'academic.prospect_student_groups';

    protected $fillable = [
        'group',
        'admission_id',
        'capacity',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AdmissionProspectGroupFactory::new();
    }

    public function getGroupAttribute($value)
    {
        return Str::upper($value);
    }

    public function getAdmission()
    {
        return $this->hasOne('Modules\Academic\Entities\Admission', 'id', 'admission_id');
    }

    public function getAdmissionProspect()
    {
        $instance = $this->hasMany('Modules\Academic\Entities\AdmissionProspect', 'prospect_group_id', 'id');
        $instance->where('is_active', 1);
        return $instance;
    }
}
