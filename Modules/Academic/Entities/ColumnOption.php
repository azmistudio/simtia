<?php

namespace Modules\Academic\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ColumnOption extends Model
{
    use HasFactory;

    protected $table = 'academic.column_options';

    protected $fillable = [
        'column_id',
        'name',
        'order',
        'is_active',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Academic\Database\factories\ColumnOptionFactory::new();
    }

    public function getNameAttribute($value)
    {
        return Str::title($value);
    }

    public function getColumn()
    {
        return $this->hasOne('Modules\Academic\Entities\Columns', 'id', 'column_id');
    }

}
