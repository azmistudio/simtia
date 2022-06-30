<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Academic\Entities\AcademicColumnOption;

class ProspectStudentView extends Model
{
    use HasFactory;

    protected $table = 'academic.prospect_students_view';

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ProspectStudentViewFactory::new();
    }

    public function getValuesAttribute($value)
    {
        return $this->getColumnName($value);
    }

    function getColumnName($param)
    {
        $query = AcademicColumnOption::where('id', intval($param))->select('name')->first();
        return is_null($query) ? $param : $query->name;
    }
}
