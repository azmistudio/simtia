<?php

namespace Modules\Finance\Repositories\Journal;

use Illuminate\Http\Request;

interface JournalVoucherRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
}