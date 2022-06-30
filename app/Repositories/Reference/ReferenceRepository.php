<?php

namespace App\Repositories\Reference;

use Illuminate\Http\Request;

interface ReferenceRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function show($search, $is_one);
	public function data(Request $request);
	public function destroy($id, $subject);
}