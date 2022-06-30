<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'msg_type',
        'items',
        'is_read',
    ];

    public function getUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
