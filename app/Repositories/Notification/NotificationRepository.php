<?php

namespace App\Repositories\Notification;

use Illuminate\Http\Request;

interface NotificationRepository
{
	public function create(Request $request);
	public function update(Request $request);
	public function data(Request $request);
	public function destroy($id);
}