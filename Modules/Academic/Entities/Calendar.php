<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Calendar extends Model
{
    use HasFactory;

    protected $table = 'academic.calendars';

    protected $fillable = [
        'schoolyear_id',
        'description',
        'is_active',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AcademicCalendarFactory::new();
    }

    public function getSchoolYear()
    {
        return $this->hasOne('Modules\Academic\Entities\SchoolYear', 'id', 'schoolyear_id');
    }
}
