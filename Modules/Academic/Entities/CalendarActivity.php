<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CalendarActivity extends Model
{
    use HasFactory;

    protected $table = 'academic.calendar_activities';
    protected $fillable = [
        'calendar_id',
        'start',
        'end',
        'activity',
        'description',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AcademicCalendarActivityFactory::new();
    }

    public function getCalendar()
    {
        return $this->hasOne('Modules\Academic\Entities\Calendar', 'id', 'calendar_id');
    }
}
