<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookYear extends Model
{
    use HasFactory;

    protected $table = 'finance.book_years';

    protected $fillable = [
        'department_id',
        'schoolyear_id',
        'book_year',
        'start_date',
        'end_date',
        'prefix',
        'remark',
        'number',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\BookYearFactory::new();
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getSchoolYear()
    {
        return $this->hasOne('Modules\Academic\Entities\SchoolYear', 'id', 'schoolyear_id');
    }
}
