<?php

namespace App\Repositories\Group;

use Illuminate\Http\Request;

interface GroupRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($id);
	public function data(Request $request);
	public function destroy($id, $subject);
}