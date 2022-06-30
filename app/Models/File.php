<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $table = 'files';

    protected $fillable = [
        'department_id',
        'source_id',
        'source_name',
        'original_name',
        'extension',
        'size',
        'mime',
        'name',
        'remark',
    ];

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
}
