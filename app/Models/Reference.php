<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reference extends Model
{
    use HasFactory;

    protected $table = 'references';

    protected $fillable = [
        'code',
        'name',
        'category',
        'remark',
        'order',
        'parent',
    ];

    public function getNameAttribute($value)
    {
        return Str::upper($value);
    }
}
