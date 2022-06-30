<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ColumnProspectStudent extends Model
{
    use HasFactory;

    protected $table = 'academic.column_prospect_students';

    protected $fillable = [
        'prospect_student_id',
        'column_id',
        'type',
        'values'
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ColumnProspectStudentFactory::new();
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
