<?php

namespace Modules\HR\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'public.employees';
    protected $dates = ['dob','work_start'];
    protected $fillable = [
        'employee_id',
        'name',
        'title_first',
        'title_end',
        'gender',
        'pob',
        'dob',
        'religion',
        'tribe',
        'section',
        'marital',
        'national_id',
        'address',
        'phone',
        'mobile',
        'email',
        'photo',
        'work_start',
        'is_active',
        'is_retired',
        'logged',
    ];

    protected $attributes = [
        'religion' => 1,
        'is_retired' => 0
    ];
    
    protected static function newFactory()
    {
        return \Modules\HR\Database\factories\EmployeeFactory::new();
    }

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }

    public function getPobAttribute($value)
    {
        return Str::title($value);
    }

    public function getSection()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'section');
    }

    public function getTribe()
    {
        return $this->hasOne('App\Models\Reference', 'id', 'tribe');
    }
}
