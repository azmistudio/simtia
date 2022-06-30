<?php

namespace App\Repositories\General;

use Illuminate\Http\Request;

interface RoomRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
	public function combogrid(Request $request);
}