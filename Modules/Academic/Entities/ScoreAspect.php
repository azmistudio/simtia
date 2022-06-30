<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ScoreAspect extends Model
{
    use HasFactory;

    protected $table = 'academic.score_aspects';

    protected $fillable = [
        'basis',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ScoreAspectFactory::new();
    }

    public function getBasisAttribute($value)
    {
        return Str::upper($value);
    }

    public function getRemarkAttribute($value)
    {
        return Str::title($value);
    }
}
