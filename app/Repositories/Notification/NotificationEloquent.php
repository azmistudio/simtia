<?php

namespace App\Repositories\Notification;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Models\Notification;
use Carbon\Carbon;

class NotificationEloquent implements NotificationRepository
{

	use AuditLogTrait;

	public function create(Request $request)
	{
		$payload = $request->all();
		return Notification::create($payload);
	}

	public function update(Request $request)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        return Notification::where('id', $request->id)->update($payload);
	}


	public function data(Request $request)
	{

	}

	public function destroy($id)
	{
		$request = new Request();
		return Notification::destroy($id);
	}

}