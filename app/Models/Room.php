<?php

namespace App\Models;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $fillable = [
        'name',
        'is_employee',
        'gender',
        'capacity',
        'employee_id',
        'department_id',
        'logged',
    ];

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }

    public function getEmployee()
    {
        return $this->hasOne('Modules\HR\Entities\Employee', 'id', 'employee_id');
    }

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }

    public function getOccupied($room_id)
    {
        return DB::table('academic.room_placements')->where('room_id', $room_id)->count();
    }
}
