<?php

namespace Modules\Finance\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestedUser extends Model
{
    use HasFactory;

    protected $table = 'finance.requested_users';

    protected $fillable = [
        'name',
        'remark',
        'logged',
    ];
    
    protected static function newFactory()
    {
        return \Modules\Finance\Database\factories\RequestedUserFactory::new();
    }
}
