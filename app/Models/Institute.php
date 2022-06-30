<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institute extends Model
{
    use HasFactory;

    protected $table = 'institutes';

    protected $fillable = [
        'name',
        'email',
        'website',
        'address',
        'phone',
        'mobile',
        'fax',
        'logo',
        'logged',
        'department_id',
    ];

    public function getDepartment()
    {
        return $this->hasOne('App\Models\Department', 'id', 'department_id');
    }
}
