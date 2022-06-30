<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class LessonGroup extends Model
{
    use HasFactory;

    protected $table = 'academic.lesson_groups';

    protected $fillable = [
        'code',
        'group',
        'order',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\LessonGroupFactory::new();
    }

    public function getCodeAttribute($value)
    {
        return Str::upper($value);
    }

    public function getGroupAttribute($value)
    {
        return Str::title($value);
    }
}
