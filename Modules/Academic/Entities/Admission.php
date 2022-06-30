<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Admission extends Model
{
    use HasFactory;

    protected $table = 'academic.admissions';

    protected $fillable = [
        'department_id',
        'name',
        'prefix',
        'remark',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AdmissionFactory::new();
    }

    public function getNameAttribute($value)
    {
        return Str::upper($value);
    }

    public function getPrefixAttribute($value)
    {
        return Str::upper($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getProspectiveGroup()
    {
        return $this->hasMany('Modules\Academic\Entities\AdmissionProspectGroup', 'admission_id', 'id');
    }
}
