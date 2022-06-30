<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Columns extends Model
{
    use HasFactory;

    protected $table = 'academic.columns';

    protected $fillable = [
        'department_id',
        'name',
        'remark',
        'type',
        'order',
        'is_active',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ColumnsFactory::new();
    }

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

}
