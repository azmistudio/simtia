<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ColumnStudent extends Model
{
    use HasFactory;

    protected $table = 'academic.column_students';

    protected $fillable = [
        'student_id',
        'column_id',
        'type',
        'values'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ColumnStudentFactory::new();
    }

    public function getStudent()
    {
        return $this->hasOne('Modules\Academic\Entities\Students', 'id', 'student_id');
    }

    public function getColumn()
    {
        return $this->hasOne('Modules\Academic\Entities\Columns', 'id', 'column_id');
    }

    public function getColumnOption()
    {
        return $this->hasOne('Modules\Academic\Entities\ColumnOption', 'id', 'values');
    }
}
