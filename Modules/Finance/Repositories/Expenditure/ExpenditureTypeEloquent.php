<?php

namespace Modules\Finance\Repositories\Expenditure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\ExpenditureType;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\Code;
use Carbon\Carbon;

class ExpenditureTypeEloquent implements ExpenditureTypeRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return ExpenditureType::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['is_all','created_at', '_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return ExpenditureType::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = ExpenditureType::select(
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

	public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return ExpenditureType::destroy($id);
    }

    public function combobox($id, $department_id)
    {
        $query = Code::select('id','name')->where('category_id', $id)->where('department_id', $department_id)->orderBy('code')->get();
        $results[] = array('id' => 0, 'name' => '---');
        foreach ($query as $val) 
        {
            $results[] = array(
                'id' => $val->id,
                'name' => $val->name,
            );
        }
        return $results;
    }

    public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = ExpenditureType::select(
                    'finance.expenditure_types.id',
                    'finance.expenditure_types.department_id',
                    'finance.expenditure_types.name', 
                    DB::raw('finance.expenditure_types.debit_account as account_debit'), 
                    DB::raw('finance.expenditure_types.credit_account as account_credit'), 
                    DB::raw('finance.book_years.id as bookyear_id'),
                    'finance.book_years.book_year', 
                )
                ->join('finance.book_years','finance.book_years.department_id','=','finance.expenditure_types.department_id')
                ->where('finance.book_years.is_active', 1);
        // filter
        $filter = isset($request->q) ? $request->q : '';
        $department_id = isset($request->department_id) ? $request->department_id : -1;
        if ($department_id > 0)
        {
            $query = $query->where('finance.expenditure_types.department_id', $department_id);
        }
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($filter).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'name' => $request->name, 
				'debit_account' => $request->debit_account, 
				'credit_account' => $request->credit_account, 
				'amount' => $request->amount, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = ExpenditureType::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'name' => $query->name, 
				'debit_account' => $query->debit_account, 
				'credit_account' => $query->credit_account, 
				'amount' => $query->amount, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'name' => $request->has('name') ? $request->name : $query->name, 
				'debit_account' => $request->has('debit_account') ? $request->debit_account : $query->debit_account, 
				'credit_account' => $request->has('credit_account') ? $request->credit_account : $query->credit_account, 
				'amount' => $request->has('amount') ? $request->amount : $query->amount, 
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