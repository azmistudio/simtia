<?php

namespace Modules\Finance\Repositories\Journal;

use Illuminate\Http\Request;

interface JournalRepository
{
	public function create(Request $request, $subject);
	public function update(Request $request, $subject);
	public function data(Request $request);
	public function store($trans_date, $remark, $cash_no, $bookyear_id, $source, $department_id);
	
	public function createDetail($journal_id, $account_id, $debit, $credit, $uuid);
	public function updateDetail($journal_id, $account_id, $amount, $amount_new, $uuid, $is_debit);
	public function dataDetail(Request $request);
	public function totalDetail(Request $request);
	
	public function getAccount($journal_id, $category_account, $receipt_major_id);
	public function dataTransaction(Request $request);
	public function list($startdate, $lastdate, $bookyear_id, $department_id);
	public function listTotal($startdate, $lastdate, $bookyear_id, $department_id);
	public function subtotal($category_id, $startdate, $lastdate, $bookyear_id);
	public function dataTrialBalance(Request $request);
	public function equityChange($startdate, $lastdate, $bookyear_id);
	
	public function cashflowIncomes($startdate, $lastdate, $bookyear_id);
	public function cashflowExpense($startdate, $lastdate, $bookyear_id);
	public function cashflowReceivables($startdate, $lastdate, $bookyear_id);
	public function cashflowReceivablesReduce($startdate, $lastdate, $bookyear_id);
	public function cashflowPayableReduce($startdate, $lastdate, $bookyear_id);
	public function cashflowPayableRaise($startdate, $lastdate, $bookyear_id);
	public function cashflowEquities($startdate, $lastdate, $bookyear_id);
	public function cashflowEquitiesWithdrawal($startdate, $lastdate, $bookyear_id);
	public function cashflowInvestment($startdate, $lastdate, $bookyear_id);
	public function cashflowBeginBalance($startdate, $lastdate, $bookyear_id);

	public function checkBalance($bookyear_id, $start_date, $close_date);
	public function getActivaPasiva($pos, $bookyear_id, $start_date, $close_date);
	public function getRetainedEarning($bookyear_id, $start_date, $close_date);

}