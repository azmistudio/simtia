<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;
use App\Models\AuditLog;
use App\Models\User;

trait AuditLogTrait 
{

	public function logTransaction($user, $string, $before, $after) 
	{
        $model = User::where('email', auth()->user()->email)->first();
        $logging = new AuditLog;
        $logging->user = $user != '#' ? $user : auth()->user()->email;
        $logging->ip = request()->ip();
        $logging->browser = \Request::header('user-agent');
        $logging->remark = $string;
        $logging->before = $before;
        $logging->after = $after;
        $logging->department_id = $model->department_id;
        $logging->save();
	}

}