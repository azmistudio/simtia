<?php

namespace Modules\Finance\Repositories\Expenditure;

use Illuminate\Http\Request;

interface ExpenditureRepository
{
	public function create(Request $request, $subject);
	public function createDetail($expenditure_id, $account_id, $remark, $amount);
	public function update(Request $request, $subject);
	public function show($id);
	public function data(Request $request);
	public function dataDetail(Request $request);
	public function dataJournal($id);
	public function dataRequested(Request $request);

	public function dataTransaction(Request $request);
	public function dataReceiptJournal(Request $request);

}