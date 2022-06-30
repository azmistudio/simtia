<?php

namespace Modules\Finance\Repositories\Journal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Entities\JournalVoucher;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\JournalView;
use Carbon\Carbon;

class JournalVoucherEloquent implements JournalVoucherRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return JournalVoucher::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['bookyear_id','rows','totalDebit','totalCredit','created_at', '_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return JournalVoucher::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request, 'asc', 'id');
        $query = JournalVoucher::select(
			            'finance.journal_vouchers.id',
			            'finance.journal_vouchers.journal_id',
			            'finance.journal_vouchers.purpose',
			            'finance.journal_vouchers.department_id',
			            'finance.journal_vouchers.trans_date',
			            'finance.journals.cash_no',
			            'finance.journals.bookyear_id',
			            'finance.book_years.book_year',
			        )
			        ->join('finance.journals','finance.journals.id','=','finance.journal_vouchers.journal_id')
			        ->join('finance.book_years','finance.book_years.id','=','finance.journals.bookyear_id')
			        ->where('finance.journals.source','journalvoucher');
		        // filter
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '') 
        {
            $query = $query->where('finance.journal_vouchers.department_id', $dept);
        }
        $fstart = isset($request->params['fstart']) ? $request->params['fstart'] : date('d/m/Y', strtotime('-1 months'));
        if ($fstart != '') 
        {
            $query = $query->whereRaw('finance.journal_vouchers.trans_date >= ?', Carbon::createFromFormat('d/m/Y',$fstart)->format('Y-m-d'));
        }
        $fend = isset($request->params['fend']) ? $request->params['fend'] : date('d/m/Y');
        if ($fend != '') 
        {
            $query = $query->whereRaw('finance.journal_vouchers.trans_date <= ?', Carbon::createFromFormat('d/m/Y',$fend)->format('Y-m-d'));
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(finance.journals.cash_no) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
        	$model['department'] = $model->getDepartment->name;
        	$model['date_journal'] = $this->formatDate($model['trans_date'],'local');
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
        	return $model;
        });
        return $result;
	}

    private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'journal_id' => $request->journal_id, 
				'purpose' => $request->purpose, 
				'trans_date' => $request->trans_date, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = JournalVoucher::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'journal_id' => $query->journal_id, 
				'purpose' => $query->purpose, 
				'trans_date' => $query->trans_date, 
			);
			$after = array(
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id, 
				'purpose' => $request->has('purpose') ? $request->purpose : $query->purpose, 
				'trans_date' => $request->has('trans_date') ? $request->trans_date : $query->trans_date, 
			);
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		} 
	}
}