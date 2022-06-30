<?php

namespace App\Repositories\User;

use Illuminate\Http\Request;

interface UserRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function destroy($id, $subject);
}