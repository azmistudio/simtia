<?php

namespace Modules\Finance\Repositories\Reference;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\Code;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\JournalDetail;
use Modules\Finance\Entities\BeginBalance;
use Carbon\Carbon;

class CodeEloquent implements CodeRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Code::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['prefix','balance','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return Code::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$mains = DB::table('finance.code_categories')->orderBy('order')->get();
        $i = 0;
        foreach ($mains as $main) 
        {
            $main_children[] = array(
                'id' => '-2'.$main->id,
                'name' => '<b>'.$main->category.'</b>',
                'code' => '',
                'balance' => '',
                'position' => $main->position,
                'children' => $this->getChildren($main->id)
            );
            $i++;
        }
        //
        $result[] = array(
            'id' => '-1',
            'name' => 'Semua Kategori',
            'code' => '',
            'balance' => '',
            'children' => $main_children
        );
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return Code::destroy($id);
	}

	public function dataGrid(Request $request)
    {
        $sub_query = BeginBalance::select('account_id','total','pos')->where('bookyear_id', $request->bookyear_id);
        $main_query = Code::select(
                            'finance.codes.id',
                            'finance.codes.code',
                            'finance.codes.name',
                            DB::raw('COALESCE(balances.total,0) as total'),
                            'balances.pos'
                        )
                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                        ->leftJoinSub($sub_query, 'balances', function($join){
                            $join->on('balances.account_id','=','finance.codes.id');
                        })
                        ->whereRaw('finance.codes.id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0)')
                        ->where('finance.code_categories.position', $request->pos)
                        ->orderBy('finance.codes.code')
                        ->get();
        $total = $this->totalBalance($request->pos, $request->bookyear_id);
        $footer[] = array(
            'name' => '<b>Total</b>',
            'total' => $total->value,
        );
        $result["rows"] = $main_query;
        $result["footer"] = $footer;
        return $result;
    }

    public function totalBalance($position, $bookyear_id)
    {
        return Code::selectRaw('COALESCE(sum(finance.begin_balances.total),0) as value')
                    ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                    ->leftJoin('finance.begin_balances','finance.begin_balances.account_id','=','finance.codes.id')
                    ->whereRaw('finance.codes.id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0)')
                    ->where('finance.code_categories.position', $position)
                    ->where('finance.begin_balances.bookyear_id', $bookyear_id)
                    ->first();
    }

    public function combobox($id, $like = '', $parent = false)
    {
        if ($parent)
        {
            $query = Code::select('id','code','name')->where('category_id', $id)->where('parent', 0)->where('code','like','%'.$like.'%')->orderBy('code')->get();
        } else {
            $query = Code::select('id','code','name')->where('category_id', $id)->whereRaw('id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0)')->where('code','like','%'.$like.'%')->orderBy('code')->get();
        }
        $results[] = array('id' => 0, 'name' => '---');
        foreach ($query as $val) 
        {
            $results[] = array(
                'id' => $val->id,
                'name' => $val->code .' | '. $val->name,
            );
        }
        return $results;
    }

	public function combogrid(Request $request)
    {
        $page = isset($request->page) ? intval($request->page) : 1;
        $rows = isset($request->rows) ? intval($request->rows) : 10;
        $query = Code::whereRaw('id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0)')->orderBy('code');
        // filter
        $filter = isset($request->q) ? Str::lower($request->q) : '';
        $position = isset($request->pos) ? $request->pos : '';
        if ($position != '') 
        {
            $query = $query->whereHas('getCategory', function ($query_sub) use ($position) {
                            $query_sub->where('position','<>',$position);
                        });
        }
        $category = isset($request->category) ? $request->category : '';
        if ($category != 0) 
        {
            $query = $query->whereHas('getCategory', function ($query_sub) use ($category) {
                            $query_sub->where('id',$category);
                        });
        }
        if ($filter != '') 
        {
            $query = $query->where('code','like','%'.$filter.'%')->orWhereRaw('LOWER(name) like ?',['%'.$filter.'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($page - 1) * $rows)->take($rows)->get()->map(function ($model) {
            $model['category_id'] = $model->getCategory->category;
            return $model->only(['id','code','name','category_id']);
        });
        return $result;
    }

    public function getBalance($account_id, $department_id, $date)
    {
        $accounts = Code::where('parent', $account_id)->get();
        $sumtotal = 0;
        foreach ($accounts as $account) 
        {
            $sumtotal += $this->getBalanceSub($account->id, $department_id, $date);
        }
        return $sumtotal;
    }

    public function getBalanceSub($account_id, $department_id, $date)
    {
        $bookyear = $this->getActiveBookYear();
        $begin_balance = BeginBalance::select(
                                'finance.begin_balances.total',
                                'finance.begin_balances.pos',
                                'finance.code_categories.position',
                                'finance.codes.code',
                            )
                            ->join('finance.codes','finance.codes.id','=','finance.begin_balances.account_id')
                            ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                            ->where('bookyear_id', $bookyear->id)
                            ->where('account_id', $account_id)
                            ->first();
        // get journal
        $journals = JournalDetail::select(
                            DB::raw('SUM(debit) as total_debit'),
                            DB::raw('SUM(credit) as total_credit'),
                            'finance.code_categories.position'
                        )
                        ->join('finance.journals','finance.journals.id','=','finance.journal_details.journal_id')
                        ->join('finance.codes','finance.codes.id','=','finance.journal_details.account_id')
                        ->join('finance.code_categories','finance.code_categories.id','=','finance.codes.category_id')
                        ->where('finance.journals.bookyear_id', $bookyear->id)
                        ->where('finance.journals.source','<>','begin_balances')
                        ->whereDate('finance.journals.journal_date', '>=', $bookyear->start_date)
                        ->whereDate('finance.journals.journal_date', '<=', $bookyear->end_date)
                        ->where('finance.journal_details.account_id', $account_id);
        //
        if ($department_id > 1)
        {
            $journals = $journals->where('finance.journals.department_id', $department_id);
        }
        $journals = $journals->groupBy('finance.code_categories.position')->first();

        $transaction = 0;
        //
        if (isset($journals->position))
        {
            $balance_total = isset($begin_balance->total) ? $begin_balance->total : 0;
            $transaction = $journals->position == 'D' ? $balance_total + ($journals->total_debit - $journals->total_credit) : $balance_total + ($journals->total_credit - $journals->total_debit);
        } else {
            $transaction = isset($begin_balance->total) ? $begin_balance->total : 0;
        }
        return $transaction;
    }

    public function listSummary($startdate, $lastdate, $bookyear_id) 
    {
        return DB::select("SELECT * FROM finance.fn_get_summary_ledger_account(:startdate,:lastdate,:bookyear_id)", 
                [
                    'startdate' => $startdate, 
                    'lastdate' => $lastdate, 
                    'bookyear_id' => $bookyear_id
                ]);
    }

    public function list($balance) 
    {
        $query = Code::select('finance.codes.id','finance.codes.code','finance.codes.name');
        if ($balance)
        {
            $query = $query->join('finance.journal_details','finance.journal_details.account_id','=','finance.codes.id');
        } else {
            $query = $query->leftJoin('finance.journal_details','finance.journal_details.account_id','=','finance.codes.id');
        }
        return $query->whereRaw('finance.codes.id NOT IN (SELECT parent FROM finance.codes WHERE parent <> 0)')
                ->groupBy('finance.codes.id','finance.codes.code','finance.codes.name')
                ->orderBy('finance.codes.code')
                ->get();
    }

    public function listBalance($lastdate, $bookyear_id)
    {
        return DB::select("SELECT * FROM finance.fn_get_end_balance_account(:lastdate,:bookyear_id)", ['lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]);
    }

    public function listProfitLoss($category_id, $startdate, $lastdate, $bookyear_id)
    {
        return DB::select(
                    "SELECT * FROM finance.fn_get_profit_loss(:category_id,:startdate,:lastdate,:bookyear_id)", 
                    ['category_id' => $category_id, 'startdate' => $startdate, 'lastdate' => $lastdate, 'bookyear_id' => $bookyear_id]
                );
    }

    public function accountBalance($code, $lastdate, $department_id)
    {
        $codes = Code::where('code','like','%'.$code.'%')->orderBy('code')->get();
        foreach ($codes as $code) 
        {
            $result[] = array(
                'id' => $code->id,
                'category_id' => $code->category_id,
                'code' => $code->code,
                'name' => $code->name,
                'parent' => $code->parent,
                'balance_total' => $this->getBalance($code->id, $department_id, $lastdate),
                'balance' => $this->getBalanceSub($code->id, $department_id, $lastdate)
            );
        }
        return $result;
    }

    private function getChildren($id)
    {
        $query = Code::where('category_id', $id)->where('parent', 0)->orderBy('code')->get();
        $children = array();
        foreach ($query as $val) 
        {
            $children[] = array(
                'id' => $val->id,
                'name' => $val->name,
                'code' => $val->code,
                'balance' => number_format($this->getBalance($val->id, 1, date('Y-m-d'))),
                'position' => $val->getCategory->position,
                'parent' => $val->parent,
                'children' => $this->getChildrenSub($val->id)
            );
        }
        return $children;
    }

    private function getChildrenSub($id)
    {
        $query = Code::where('parent', $id)->orderBy('code')->get();
        $children = array();
        foreach ($query as $val) 
        {
            $children[] = array
            (
                'id' => $val->id,
                'name' => $val->name,
                'code' => $val->code,
                'balance' => number_format($this->getBalanceSub($val->id, 1, date('Y-m-d'))),
                'position' => $val->getCategory->position,
                'parent' => $val->parent,
                'children' => array()
            );
        }
        return $children;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'book_year' => $request->book_year, 
				'start_date' => $request->start_date, 
				'end_date' => $request->end_date, 
				'prefix' => $request->prefix, 
				'number' => $request->number, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Code::find($model_id);
			$before = array(
				'book_year' => $query->book_year, 
				'start_date' => $query->start_date, 
				'end_date' => $query->end_date, 
				'prefix' => $query->prefix, 
				'number' => $query->number, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'book_year' => $request->has('book_year') ? $request->book_year : $query->book_year, 
				'start_date' => $request->has('start_date') ? $request->start_date : $query->start_date, 
				'end_date' => $request->has('end_date') ? $request->end_date : $query->end_date, 
				'prefix' => $request->has('prefix') ? $request->prefix : $query->prefix, 
				'number' => $request->has('number') ? $request->number : $query->number, 
				'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active, 
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