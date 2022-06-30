<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PresenceDaily extends Model
{
    use HasFactory;

    protected $table = 'academic.presence_dailies';
    protected $dates = ['start_date','end_date'];
    protected $fillable = [
        'class_id',
        'semester_id',
        'start_date',
        'end_date',
        'active_day',
        'logged',
    ];

    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\PresenceDailyFactory::new();
    }

    public function getClass()
    {
        return $this->hasOne('Modules\Academic\Entities\Classes', 'id', 'class_id');
    }

    public function getSemester()
    {
        return $this->hasOne('Modules\Academic\Entities\Semester', 'id', 'semester_id');
    }
}
