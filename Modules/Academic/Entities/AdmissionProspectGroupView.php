<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Modules\Academic\Entities\Admission;

class AdmissionProspectGroupView extends Model
{
    use HasFactory;

    protected $table = 'academic.prospect_groups_view';

    protected $fillable = [];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\AdmissionProspectGroupViewFactory::new();
    }

    public function getAdmissionIdAttribute($value)
    {
        return $this->getAdmissionName($value);
    }

    public function getGroupAttribute($value)
    {
        return Str::upper($value);
    }

    function getAdmissionName($param)
    {
        return Admission::where('id', $param)->pluck('name')->first();
    }
}
