<?php

namespace Modules\Finance\Repositories\Journal;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Finance\Entities\Journal;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\ReceiptCategory;
use Modules\Finance\Entities\JournalView;
use Carbon\Carbon;

class JournalEloquent implements JournalRepository
{

	use AuditLogTrait;
	use HelperTrait;
    use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Journal::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['is_all','created_at', '_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return Journal::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request, 'asc', 'id');
        $query = Journal::where('finance.journals.source','journalvoucher');
        // filter
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '') 
        {
            $query = $query->where('finance.journals.department_id', $dept);
        }
        $fstart = isset($request->params['fstart']) ? $request->params['fstart'] : date('d/m/Y', strtotime('-1 months'));
        if ($fstart != '') 
        {
            $query = $query->whereRaw('finance.journals.journal_date >= ?', Carbon::createFromFormat('d/m/Y', $fstart)->format('Y-m-d'));
        }
        $fend = isset($request->params['fend']) ? $request->params['fend'] : date('d/m/Y');
        if ($fend != '') 
        {
            $query = $query->whereRaw('finance.journals.journal_date <= ?', Carbon::createFromFormat('d/m/Y', $fend)->format('Y-m-d'));
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(finance.journals.cash_no) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['journal_date'] = $this->formatDate($model['journal_date'],'iso');
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
            $model['department'] = $model->getDepartment->name;
            $model['book_year'] = $model->getBookYear->book_year;
            return $model;
        });
        return $result;
	}

	public function store($trans_date, $remark, $cash_no, $bookyear_id, $source, $department_id)
    {
        return Journal::create([
                    'journal_date' => $trans_date,
                    'transaction' => $remark,
                    'cash_no' => $cash_no,
                    'employee_id' => auth()->user()->id,
                    'bookyear_id' => $bookyear_id,
                    'source' => $source,
                    'department_id' => $department_id,
                    'logged' => auth()->user()->email,
                ]);
    }
	
	public function createDetail($journal_id, $account_id, $debit, $credit, $uuid)
    {
        return JournalDetail::create([
                    'journal_id' => $journal_id,
                    'account_id' => $account_id,
                    'debit' => $debit,
                    'credit' => $credit,
                    'uuid' => $uuid,
                    'logged' => auth()->user()->email,
                ]); 
    }

    public function updateDetail($journal_id, $account_id, $amount, $amount_new, $uuid, $is_debit)
    {
        if ($is_debit)
        {
            return JournalDetail::where('journal_id', $journal_id)
                    ->where('account_id', $account_id)
                    ->where('debit', $amount_new)
                    ->update([
                        'debit' => $amount,
                        'uuid' => $uuid,
                        'logged' => auth()->user()->email,
                    ]);
        } else {
            return JournalDetail::where('journal_id', $journal_id)
                    ->where('account_id', $account_id)
                    ->where('credit', $amount_new)
                    ->update([
                        'credit' => $amount,
                        'uuid' => $uuid,
                        'logged' => auth()->user()->email,
                    ]);
        }
    }

    public function dataDetail(Request $request)
    {
        // query
        $query = JournalDetail::select(
                        'finance.codes.id',
                        'finance.journal_details.debit',
                        'finance.journal_details.credit',
                        'finance.codes.code',
                        'finance.codes.name',
                    )
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->where('finance.journal_details.journal_id', $request->journal_id)
                    ->orderBy('finance.journal_details.id')
                    ->get();
        // result
        $result["rows"] = $query;
        return $result;
    }

    public function totalDetail(Request $request)
    {
        return JournalDetail::select(DB::raw('SUM(debit) as total_debit'),DB::raw('SUM(credit) as total_credit'))->where('journal_id', $request->journal_id)->first();
    }

    public function getAccount($journal_id, $category_account, $receipt_major_id)
    {
        $like = '%%';
        switch ($category_account) 
        {
            case 'HUTANG':
                $like = '%2-%';
                break;
            case 'MODAL':
                $like = '%3-%';
                break;
            case 'BIAYA':
                $like = '%5-%';
                break;
            case 'PIUTANG':
                $like = '%1-2%';
                break;
            case 'ASET':
                $like = '%1-3%';
                break;
            case 'DEPRESIASI':
                $like = '%1-4%';
                break;
            default:
                $like = '%1-1%';
                break;
        }
        if ($category_account == 'PENDAPATAN')
        {
            $query = JournalDetail::select('finance.journal_details.account_id','finance.codes.code')
                        ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                        ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                        ->where('finance.journal_details.journal_id', $journal_id)
                        ->where('finance.code_categories.category', 'PENDAPATAN')
                        ->where('finance.journal_details.debit',0)
                        ->where('finance.journal_details.credit','>',0)
                        ->first();
        } elseif ($category_account == 'DISKON') {
            $query = JournalDetail::select('finance.journal_details.account_id','finance.codes.code')
                        ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                        ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                        ->where('finance.journal_details.journal_id', $journal_id)
                        ->where('finance.code_categories.category', 'PENDAPATAN')
                        ->where('finance.codes.name','like','%Diskon%')
                        ->where('finance.journal_details.debit','>',0)
                        ->where('finance.journal_details.credit',0)
                        ->first();
        } else {
            $query = JournalDetail::select('finance.journal_details.account_id','finance.codes.code')
                        ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                        ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                        ->where('finance.journal_details.journal_id', $journal_id)
                        ->where('finance.code_categories.category','<>', 'PENDAPATAN')
                        ->where('finance.codes.code','like', $like)
                        ->first();
        }
        if (!empty($query)) 
        {
            return $query->account_id;
        } else {
            switch ($category_account) 
            {
                case 'KAS':
                    $column = 'cash_account';
                    break;
                case 'PIUTANG':
                    $column = 'receivable_account';
                    break;
                case 'PENDAPATAN':
                    $column = 'receipt_account';
                    break;
                default:
                    $column = 'discount_account';
                    break;
            }
            $account_id = DB::table('finance.receipt_types')->select($column)->where('id', $receipt_major_id)->value($column);
            return !empty($account_id) ? $account_id : 0;
        }
    }

    public function dataTransaction(Request $request)
    {
        $param = $this->gridRequest($request, 'desc', 'id');
        $query = JournalView::where('bookyear_id', $request->bookyear_id)
                    ->whereDate('journal_date', '>=', $this->formatDate($request->start_date,'sys'))
                    ->whereDate('journal_date', '<=', $this->formatDate($request->end_date,'sys'));
        if ($request->department_id > 1)
        {
            $query = $query->where('department_id', $request->department_id);
        }
        $cash_no = isset($request->cash_no) ? $request->cash_no : '';
        if ($cash_no != '') 
        {
            $query = $query->whereRaw('LOWER(cash_no) like ?', ['%'.Str::lower($cash_no).'%']);
        }
        $totals = $this->totalTransaction($request->bookyear_id, $request->department_id, $request->start_date, $request->end_date);
        $footer[] = array(
            'transaction'   => '<b>Total</b>',
            'debit'         => '<b>Rp'.number_format($totals->total_debit, 2).'</b>',
            'credit'        => '<b>Rp'.number_format($totals->total_credit, 2).'</b>',
        );
        // result
        $result["total"] = $query->count();
        $result["rows"]  = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
            $model['journal'] = $model->cash_no .' / '. $this->formatDate($model->journal_date,'iso');
            return $model;
        });
        $result["footer"] = $footer;
        return $result;
    }

    public function totalTransaction($bookyear_id, $department_id, $start_date, $end_date)
    {
        $query = JournalView::select(DB::raw('SUM(debit) as total_debit'),DB::raw('SUM(credit) as total_credit'))
                    ->where('bookyear_id', $bookyear_id)
                    ->whereRaw('journal_date >= ?', $this->formatDate($start_date,'sys'))
                    ->whereRaw('journal_date <= ?', $this->formatDate($end_date,'sys'));
        if ($department_id > 1)
        {
            $query = $query->where('department_id', $department_id);
        }
        return $query->first();
    }

    public function list($startdate, $lastdate, $bookyear_id, $department_id)
    {
        $query = Journal::select(
                        'finance.journal_details.account_id',
                        'finance.journal_details.debit',
                        'finance.journal_details.credit',
                        DB::raw('finance.journals.transaction as remark'),
                        'finance.journals.source',
                        'finance.journals.journal_date',
                        DB::raw('UPPER(public.departments.name) as department'),
                    )
                    ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.journals.id')
                    ->join('public.departments','public.departments.id','=','finance.journals.department_id')
                    ->whereRaw('finance.journals.journal_date >= ?', $startdate)
                    ->whereRaw('finance.journals.journal_date <= ?', $lastdate)
                    ->where('finance.journals.bookyear_id', $bookyear_id);
        if ($department_id > 1)
        {
            $query = $query->where('finance.journals.department_id', $department_id);
        }
        return $query->get()->map(function($model){
            $model['journal_date'] = $this->formatDate($model->journal_date, 'iso');
            $model['source'] = $this->getTransactionSource()[$model->source];
            return $model;
        });
    }

    public function listTotal($startdate, $lastdate, $bookyear_id, $department_id)
    {
        $query = Journal::select(
                        'finance.journal_details.account_id',
                        DB::raw('SUM(finance.journal_details.debit) as debit'),
                        DB::raw('SUM(finance.journal_details.credit) as credit'),
                    )
                    ->join('finance.journal_details','finance.journal_details.journal_id','=','finance.journals.id')
                    ->whereRaw('finance.journals.journal_date >= ?', $startdate)
                    ->whereRaw('finance.journals.journal_date <= ?', $lastdate)
                    ->where('finance.journals.bookyear_id', $bookyear_id);
        if ($department_id > 1)
        {
            $query = $query->where('finance.journals.department_id', $department_id);
        }
        return $query->groupBy('finance.journal_details.account_id')->get();
    }

    public function subtotal($category_id, $startdate, $lastdate, $bookyear_id)
    {
        return JournalDetail::select(
                        'finance.codes.category_id',
                        DB::raw('COALESCE(SUM(finance.journal_details.debit) - SUM(finance.journal_details.credit),0) AS debit'),
                        DB::raw('COALESCE(SUM(finance.journal_details.credit) - SUM(finance.journal_details.debit),0) AS credit'),
                    )
                    ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->where('finance.codes.category_id', $category_id)
                    ->whereRaw('finance.journals.journal_date >= ?', $startdate)
                    ->whereRaw('finance.journals.journal_date <= ?', $lastdate)
                    ->where('finance.journals.bookyear_id', $bookyear_id)
                    ->groupBy('finance.codes.category_id')
                    ->first();
    }

    public function dataTrialBalance(Request $request)
    {
        $total_debit = 0;
        $total_credit = 0;
        $start_date = $this->formatDate($request->start_date,'sys');
        $end_date = $this->formatDate($request->end_date,'sys');
        // query
        $query = DB::select("
                        SELECT * FROM finance.fn_get_trial_balance(:start_date, :end_date, :bookyear_id)", 
                        ['start_date' => $start_date, 'end_date' => $end_date, 'bookyear_id' => $request->bookyear_id]
                    );
        foreach ($query as $row) 
        {
            $rows[] = array(
                'code' => $row->code,
                'name' => $row->name,
                'debit' => number_format($row->debit,2),
                'credit' => number_format($row->credit,2),
            );
            $total_debit += $row->debit;
            $total_credit += $row->credit;
        }
        $totals = $this->totalTransaction($request->bookyear_id, 0, $request->start_date, $request->end_date);
        $footer[] = array(
            'name' => '<b>Total</b>',
            'debit' => '<b>Rp'.number_format($total_debit, 2).'</b>',
            'credit' => '<b>Rp'.number_format($total_credit, 2).'</b>',
        );
        // result
        $result["rows"] = $rows;
        $result["footer"] = $footer;
        return $result;
    }

    public function equityChange($startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_equity_change_periods(:startdate, :lastdate, :bookyear_id)", 
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowIncomes($startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_cashflow_income(:startdate, :lastdate, :bookyear_id)",
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowExpense($startdate, $lastdate, $bookyear_id)
    {
        $query = DB::select(
                        "SELECT finance.fn_get_cashflow_expense(:startdate, :lastdate, :bookyear_id)",
                        ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                    );
        return $query[0]->fn_get_cashflow_expense;
    }

    public function cashflowReceivables($startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_cashflow_receivables(:startdate, :lastdate, :bookyear_id)",
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowReceivablesReduce($startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_cashflow_receivables_reduce(:startdate, :lastdate, :bookyear_id)",
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowPayableReduce($startdate, $lastdate, $bookyear_id)
    {
        $query = DB::select(
                        "SELECT finance.fn_get_cashflow_payable_reduce(:startdate, :lastdate, :bookyear_id)",
                        ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                    );
        return $query[0]->fn_get_cashflow_payable_reduce;
    }

    public function cashflowPayableRaise($startdate, $lastdate, $bookyear_id)
    {
        $query = DB::select(
                        "SELECT finance.fn_get_cashflow_payable_raise(:startdate, :lastdate, :bookyear_id)",
                        ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                    );
        return $query[0]->fn_get_cashflow_payable_raise;
    }

    public function cashflowEquities($startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_cashflow_equities(:startdate, :lastdate, :bookyear_id)",
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowEquitiesWithdrawal($startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_cashflow_equities_withdrawal(:startdate, :lastdate, :bookyear_id)",
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowInvestment($startdate, $lastdate, $bookyear_id)
    {
        return DB::select("
                    SELECT * FROM finance.fn_get_cashflow_investment(:startdate, :lastdate, :bookyear_id)",
                    ['startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function cashflowBeginBalance($startdate, $lastdate, $bookyear_id)
    {
        $lastdates = explode('-', $lastdate);
        $new_lastdate = $lastdates[0].'-'.$lastdates[1].'-01';
        $last_date = date('Y-m-t', strtotime('-1 months', strtotime($lastdates[0].'-'.$lastdates[1].'-01')));
        return JournalDetail::select(
                        DB::raw('COALESCE(SUM(finance.journal_details.debit - finance.journal_details.credit),0) AS value'),
                    )
                    ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                    ->whereRaw('finance.journals.journal_date >= ?', $startdate)
                    ->whereRaw('finance.journals.journal_date <= ?', $last_date)
                    ->where('finance.journals.bookyear_id', $bookyear_id)
                    ->where('finance.code_categories.category', 'HARTA')
                    ->first();
    }

    public function checkBalance($bookyear_id, $start_date, $close_date)
    {
        $activa = $this->getBalance($bookyear_id, $start_date, $close_date)
                    ->whereIn('finance.code_categories.category',['HARTA'])
                    ->select(DB::raw('SUM(finance.journal_details.debit - finance.journal_details.credit) as total'))
                    ->pluck('total')
                    ->first();
        //
        $pasiva = $this->getBalance($bookyear_id, $start_date, $close_date)
                    ->whereIn('finance.code_categories.category',['HUTANG','MODAL','PENDAPATAN'])
                    ->select(DB::raw('SUM(finance.journal_details.credit - finance.journal_details.debit) as total'))
                    ->pluck('total')
                    ->first();
        //
        $cost   = $this->getBalance($bookyear_id, $start_date, $close_date)
                    ->whereIn('finance.code_categories.category',['BIAYA'])
                    ->select(DB::raw('SUM(finance.journal_details.debit - finance.journal_details.credit) as total'))
                    ->pluck('total')
                    ->first();

        $pasiva = $pasiva - $cost;
        return $activa != $pasiva ? false : true;
    }

    public function getActivaPasiva($pos, $bookyear_id, $start_date, $close_date)
    {
        $params = [
            'bookyear_id_1' => $bookyear_id, 
            'bookyear_id_2' => $bookyear_id, 
            'start_date' => $this->formatDate($start_date,'sys'), 
            'close_date' => $this->formatDate($close_date,'sys')
        ];

        if ($pos == 'activa') 
        {
            return DB::select("
                        SELECT e.account_id, e.code, e.name, SUM(total) as total FROM (
                            SELECT a.account_id, b.code, b.name, a.total 
                            FROM finance.begin_balances a
                            JOIN finance.codes b ON b.id = a.account_id
                            JOIN finance.code_categories c ON c.id = b.category_id
                            WHERE a.bookyear_id = :bookyear_id_1 AND a.total <> 0 AND c.category = 'HARTA'
                            UNION ALL
                            SELECT b.account_id, c.code, c.name, SUM(b.debit - b.credit) AS total
                            FROM finance.journals a 
                            JOIN finance.journal_details b ON b.journal_id = a.id
                            JOIN finance.codes c ON c.id = b.account_id
                            JOIN finance.code_categories d ON d.id = c.category_id
                            WHERE a.bookyear_id = :bookyear_id_2 AND a.journal_date BETWEEN :start_date AND :close_date AND d.category = 'HARTA'
                            GROUP BY a.bookyear_id, b.account_id, c.code, c.name
                            ) AS e GROUP BY e.account_id, e.code, e.name
                        ORDER BY e.code
                    ", $params);
        } else {
            return DB::select("
                        SELECT e.account_id, e.code, e.name, SUM(total) as total FROM (
                            SELECT a.account_id, b.code, b.name, a.total 
                            FROM finance.begin_balances a
                            JOIN finance.codes b ON b.id = a.account_id
                            JOIN finance.code_categories c ON c.id = b.category_id
                            WHERE a.bookyear_id = :bookyear_id_1 AND a.total <> 0 AND c.category = 'HUTANG'
                            UNION ALL
                            SELECT b.account_id, c.code, c.name, SUM(b.credit - b.debit) AS total
                            FROM finance.journals a 
                            JOIN finance.journal_details b ON b.journal_id = a.id
                            JOIN finance.codes c ON c.id = b.account_id
                            JOIN finance.code_categories d ON d.id = c.category_id
                            WHERE a.bookyear_id = :bookyear_id_2 AND a.journal_date BETWEEN :start_date AND :close_date AND d.category = 'HUTANG'
                            GROUP BY a.bookyear_id, b.account_id, c.code, c.name
                            ) AS e GROUP BY e.account_id, e.code, e.name
                        ORDER BY e.code
                    ", $params);
        }
    }

    public function getRetainedEarning($bookyear_id, $start_date, $close_date)
    {
        $income = $this->getBalance($bookyear_id, $start_date, $close_date)
                    ->whereIn('finance.code_categories.category',['MODAL','PENDAPATAN'])
                    ->select(DB::raw('SUM(finance.journal_details.credit - finance.journal_details.debit) as total'))
                    ->pluck('total')
                    ->first();

        $cost   = $this->getBalance($bookyear_id, $start_date, $close_date)
                    ->whereIn('finance.code_categories.category',['BIAYA'])
                    ->select(DB::raw('SUM(finance.journal_details.debit - finance.journal_details.credit) as total'))
                    ->pluck('total')
                    ->first();

        return $income - $cost;
    }

    private function getBalance($bookyear_id, $start_date, $close_date)
    {
        return Journal::join('finance.journal_details','finance.journal_details.journal_id','=','finance.journals.id')
                    ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                    ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                    ->where('finance.journals.bookyear_id', $bookyear_id)
                    ->whereDate('journal_date','>=',$this->formatDate($start_date,'sys'))
                    ->whereDate('journal_date','<=',$this->formatDate($close_date,'sys'));
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'journal_date' => $request->journal_date, 
				'transaction' => $request->transaction, 
				'cash_no' => $request->cash_no, 
				'employee_id' => $request->employee_id, 
				'bookyear_id' => $request->bookyear_id, 
				'source' => $request->source, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Journal::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'journal_date' => $query->journal_date, 
				'transaction' => $query->transaction, 
				'cash_no' => $query->cash_no, 
				'employee_id' => $query->employee_id, 
				'bookyear_id' => $query->bookyear_id, 
				'source' => $query->source, 
			);
			$after = array(
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'journal_date' => $request->has('journal_date') ? $request->journal_date : $query->journal_date, 
				'transaction' => $request->has('transaction') ? $request->transaction : $query->transaction, 
				'cash_no' => $request->has('cash_no') ? $request->cash_no : $query->cash_no, 
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'bookyear_id' => $request->has('bookyear_id') ? $request->bookyear_id : $query->bookyear_id, 
				'source' => $request->has('source') ? $request->source : $query->source, 
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