<?php

namespace Modules\Finance\Repositories\Expenditure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\Expenditure;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\JournalDetail;
use Carbon\Carbon;

class ExpenditureEloquent implements ExpenditureRepository
{

    use ReferenceTrait;
	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Expenditure::create($payload);
	}

	public function createDetail($expenditure_id, $account_id, $remark, $amount)
    {
        return DB::table('finance.expenditure_details')->insert([
            'expenditure_id' => $expenditure_id,
            'account_id' => $account_id,
            'remark' => $remark,
            'amount' => $amount,
        ]);
    }

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), [
	        'bookyear_id',
	        'debit_account',
	        'rows',
	        'totalCredit',
			'created_at',
			'_token'
		]);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return Expenditure::where('id', $payload['id'])->update($payload);
	}

	public function show($id)
	{
		$debit_account = JournalDetail::select('journal_id','account_id')->where('credit','>', 0);
        return Expenditure::select(
                    'finance.expenditures.id',
                    'finance.expenditures.journal_id',
                    'finance.expenditures.purpose',
                    'finance.expenditures.requested_by',
                    'finance.expenditures.employee_id',
                    'finance.expenditures.student_id',
                    'finance.expenditures.requested_id',
                    'finance.expenditures.received_name',
                    'finance.expenditures.requested_name',
                    'finance.expenditures.trans_date',
                    'finance.expenditures.total',
                    'finance.expenditures.employee',
                    'finance.expenditures.remark',
                    'finance.expenditures.department_id',   
                    DB::raw('debits.account_id as debit_account'),   
                    'finance.book_years.book_year',
                    'finance.journals.bookyear_id',
                    'finance.journals.cash_no',
                    DB::raw("CONCAT(finance.codes.code,' | ',finance.codes.name) as codes"),
                )
                ->join('finance.journals','finance.journals.id','=','finance.expenditures.journal_id')
                ->join('finance.book_years','finance.book_years.id','=','finance.journals.bookyear_id')
                ->joinSub($debit_account, 'debits', function ($join){
                    $join->on('finance.expenditures.journal_id','=','debits.journal_id');
                })
                ->join('finance.codes','finance.codes.id','=','debits.account_id')
                ->where('finance.expenditures.id', $id)
                ->get()->map(function($model){
                    $model['trans_date'] = $this->formatDate($model->trans_date,'local');
                    $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
                    $model['total'] = $this->formatCurrency($model->total,'idr');
                    return $model;
                })[0];
	}

	public function data(Request $request)
	{
        $param = $this->gridRequest($request, 'desc', 'id');
        $query = Expenditure::select(
                        'finance.expenditures.id',
                        'finance.expenditures.department_id',
                        'finance.expenditures.total',
                        'finance.journals.cash_no',
                        'finance.journals.bookyear_id',
                        'finance.expenditures.requested_name',
                        'finance.expenditures.received_name',
                        'finance.expenditures.trans_date',
                    )
                    ->join('finance.journals','finance.journals.id','=','finance.expenditures.journal_id');
        // filter
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        }
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '') 
        {
            $query = $query->where('finance.expenditures.department_id', $dept);
        }
        $fstart = isset($request->params['fstart']) ? $request->params['fstart'] : date('d/m/Y', strtotime('-1 months'));
        if ($fstart != '') 
        {
            $query = $query->whereRaw('finance.expenditures.trans_date >= ?', $this->formatDate($fstart,'sys'));
        }
        $fend = isset($request->params['fend']) ? $request->params['fend'] : date('d/m/Y');
        if ($fend != '') 
        {
            $query = $query->whereRaw('finance.expenditures.trans_date <= ?', $this->formatDate($fend,'sys'));
        }
        $journal = isset($request->params['fjournal']) ? $request->params['fjournal'] : '';
        if ($journal != '') 
        {
            $query = $query->where('finance.journals.cash_no', 'like', '%'.$journal.'%');
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model->trans_date,'local');
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
            $model['department'] = $model->getDepartment->name;
            $model['total'] = $this->formatCurrency($model->total,'idr');
            return $model;
        });
        return $result;
	}

	public function dataJournal($id)
    {
        $query = DB::table('finance.expenditure_details')->select(
                        'finance.expenditure_details.expenditure_id',
                        'finance.codes.id',
                        'finance.codes.code',
                        'finance.codes.name',
                        'finance.expenditure_details.remark',
                        DB::raw('finance.expenditure_details.amount as credit')
                    )
                    ->join('finance.codes','finance.codes.id','=','finance.expenditure_details.account_id');
        if ($id > 0) 
        {
            $query = $query->where('expenditure_id', $id);
        }
        return $query->get();
    }

    public function dataDetail(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'finance.expenditures.trans_date');
        $query = Expenditure::select(
                        'finance.expenditures.id',
                        DB::raw("to_char(finance.expenditures.trans_date, 'DD-Mon-YYYY') as trans_date"),
                        DB::raw("(CASE 
                            WHEN requested_by = 1 THEN CONCAT(public.employees.employee_id,' ',INITCAP(public.employees.name),' (pegawai)')  
                            WHEN requested_by = 2 THEN CONCAT(academic.students.student_no,' ',INITCAP(academic.students.name),' (santri)')
                            WHEN requested_by = 3 THEN CONCAT(INITCAP(finance.requested_users.name), ' (pemohon lain)')  
                            END) as requested_person"
                        ),
                        'finance.expenditures.received_name',
                        'finance.expenditures.total',
                        DB::raw('INITCAP(finance.expenditures.purpose) as purpose'),
                        'finance.expenditures.employee',
                    )
                    ->join('finance.expenditure_types','finance.expenditure_types.id','=','finance.expenditures.expenditure_type_id')
                    ->leftJoin('public.employees','public.employees.id','=','finance.expenditures.employee_id')
                    ->leftJoin('academic.students','academic.students.id','=','finance.expenditures.student_id')
                    ->leftJoin('finance.requested_users','finance.requested_users.id','=','finance.expenditures.requested_id')
                    ->where('finance.expenditure_types.department_id', $request->fdept)
                    ->where('finance.expenditure_types.name', $request->fname)
                    ->whereRaw('finance.expenditures.trans_date >= ?', $this->formatDate($request->fromDate,'sys'))
                    ->whereRaw('finance.expenditures.trans_date <= ?', $this->formatDate($request->toDate,'sys'));
        //
        $totals = $this->totalExpenditure($request->fdept, $request->fname);
        $footer[] = array(
            'received_name' => 'Total',
            'total' => '<b>'.$totals->total.'</b>',
        );
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($sort, $order)->get();
        $result["footer"] = $footer;
        return $result;
    }

    private function totalExpenditure($department_id, $name)
    {
        return Expenditure::select(
                    'finance.expenditure_types.department_id',
                    'finance.expenditure_types.name',
                    DB::raw('UPPER(public.departments.name) AS department'),
                    DB::raw('SUM(finance.expenditures.total) as total')
                )
                ->join('finance.expenditure_types','finance.expenditure_types.id','=','finance.expenditures.expenditure_type_id')
                ->join('public.departments','public.departments.id','=','finance.expenditure_types.department_id')
                ->where('finance.expenditure_types.department_id', $department_id)
                ->where('finance.expenditure_types.name', $name)
                ->groupBy('finance.expenditure_types.department_id','finance.expenditure_types.name','public.departments.name')
                ->first();
    }

    public function dataRequested(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'finance.expenditure_types.id');
        $query = Expenditure::select(
                        'finance.expenditure_types.*',
                        DB::raw('UPPER(public.departments.name) AS department'),
                    )
                    ->join('public.departments','public.departments.id','=','finance.expenditure_types.department_id');
        // filter
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '') 
        {
            $query = $query->where('finance.expenditure_types.department_id', $dept);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(finance.expenditure_types.name) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    public function dataTransaction(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'finance.expenditures.trans_date');
        $query = Expenditure::select(
                        'finance.expenditures.id',
                        'finance.expenditures.trans_date',
                        'finance.expenditures.received_name',
                        'finance.expenditures.requested_name',
                        'finance.expenditures.total',
                        'finance.expenditures.remark',
                        'finance.expenditures.employee',
                        'finance.expenditure_details.amount',
                        DB::raw('finance.expenditure_details.remark as purpose'),
                    )
                    ->join('finance.journals','finance.journals.id','=','finance.expenditures.journal_id')
                    ->join('finance.expenditure_details','finance.expenditure_details.expenditure_id','=','finance.expenditures.id')
                    ->leftJoin('public.employees','public.employees.id','=','finance.expenditures.employee_id')
                    ->leftJoin('academic.students','academic.students.id','=','finance.expenditures.student_id')
                    ->leftJoin('finance.requested_users','finance.requested_users.id','=','finance.expenditures.requested_id')
                    ->where('finance.expenditures.department_id', $request->department_id)
                    ->where('finance.journals.bookyear_id', $request->bookyear_id)
                    ->whereRaw('finance.expenditures.trans_date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.expenditures.trans_date <= ?', $this->formatDate($request->end_date,'sys'));

        switch ($request->search_by) 
        {
            case 'receiver':
                $query = $query->whereRaw('LOWER(finance.expenditures.received_name) LIKE ?', ['%'.Str::lower($request->search_param).'%']);
                break;
            case 'officer':
                $query = $query->whereRaw('LOWER(finance.expenditures.employee) LIKE ?', ['%'.Str::lower($request->search_param).'%']);
                break;
            case 'purpose':
                $query = $query->whereRaw('LOWER(finance.expenditure_details.remark) LIKE ?', ['%'.Str::lower($request->search_param).'%']);
                break;
            case 'remark':
                $query = $query->whereRaw('LOWER(finance.expenditures.remark) LIKE ?', ['%'.Str::lower($request->search_param).'%']);
                break;
            default:
                $query = $query->whereRaw('LOWER(finance.expenditures.requested_name) LIKE ?', ['%'.Str::lower($request->search_param).'%']);
                break;
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model->trans_date,'iso');
            $model['total_val'] = 'Rp'.number_format($model->total,2);
            $model['amount_val'] = 'Rp'.number_format($model->amount,2);
            $model['purpose'] = '<b>Keperluan:</b> '. $model->purpose . '<br/><b>Keterangan: </b>' . $model->remark;  
            return $model;
        });
        $footer[] = array(
            'received_name' => 'Total',
            'amount_val' => '<b>Rp'.number_format($result['rows']->sum('total'),2).'</b>',
        );
        $result["footer"] = $footer;
        return $result;
    }

    public function dataReceiptJournal(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'trans_date');
        $query = Expenditure::select(
                        'finance.journals.cash_no',
                        'finance.expenditures.trans_date',
                        'finance.journals.transaction',
                        'finance.journals.source',
                        'finance.journals.bookyear_id',
                        'finance.expenditures.journal_id',
                        'finance.expenditures.employee',
                    )
                    ->join('finance.expenditure_details','finance.expenditure_details.expenditure_id','=','finance.expenditures.id')
                    ->join('finance.journals','finance.journals.id','=','finance.expenditures.journal_id')
                    ->where('finance.expenditures.department_id', $request->department_id)
                    ->where('finance.journals.bookyear_id', $request->bookyear_id)
                    ->whereRaw('finance.expenditures.trans_date::date >= ?', $this->formatDate($request->start_date,'sys'))
                    ->whereRaw('finance.expenditures.trans_date::date <= ?', $this->formatDate($request->end_date,'sys'))
                    ->groupBy(
                        'finance.journals.cash_no',
                        'finance.expenditures.trans_date',
                        'finance.journals.transaction',
                        'finance.journals.source',
                        'finance.journals.bookyear_id',
                        'finance.expenditures.journal_id',
                        'finance.expenditures.employee'
                    );

        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) {
            $model['cash_no'] = '<b>'.$this->getPrefixBookYear($model->bookyear_id) . $model->cash_no.'</b>';
            $model['trans_date'] = $this->formatDate($model->trans_date,'iso');
            $model['source'] = $this->getTransactionSource()[$model->source];
            $model['total'] = 'Rp'.number_format(JournalDetail::select(DB::raw('SUM(credit) as total_credit'))->where('journal_id', $model->journal_id)->pluck('total_credit')->first(),2);
            $model['name'] = $model->employee;
            return $model;
        });
        return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'journal_id' => $request->journal_id, 
				'purpose' => $request->purpose, 
				'employee_id' => $request->employee_id, 
				'student_id' => $request->student_id, 
				'requested_id' => $request->requested_id, 
				'received_name' => $request->received_name, 
				'trans_date' => $request->trans_date, 
				'total' => $request->total, 
				'department_id' => $request->department_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Expenditure::find($model_id);
			$before = array(
				'journal_id' => $query->journal_id, 
				'purpose' => $query->purpose, 
				'employee_id' => $query->employee_id, 
				'student_id' => $query->student_id, 
				'requested_id' => $query->requested_id, 
				'received_name' => $query->received_name, 
				'trans_date' => $query->trans_date, 
				'total' => $query->total, 
				'department_id' => $query->department_id, 
			);
			$after = array(
				'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id, 
				'purpose' => $request->has('purpose') ? $request->purpose : $query->purpose, 
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id, 
				'requested_id' => $request->has('requested_id') ? $request->requested_id : $query->requested_id, 
				'received_name' => $request->has('received_name') ? $request->received_name : $query->received_name, 
				'trans_date' => $request->has('trans_date') ? $request->trans_date : $query->trans_date, 
				'total' => $request->has('total') ? $request->total : $query->total, 
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
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