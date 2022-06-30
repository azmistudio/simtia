<?php

namespace Modules\Finance\Repositories\Saving;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\SavingType;
use Modules\Finance\Entities\BookYear;
use Carbon\Carbon;

class SavingTypeEloquent implements SavingTypeRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return SavingType::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['is_all','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return SavingType::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request, $is_employee)
    {
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = SavingType::select('*');
        // filter
        $query = $query->where('is_employee', $is_employee);
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '') 
        {
            $query = $query->where('department_id', $dept);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            return $model;
        });
        return $result;
    }

    public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return SavingType::destroy($id);
    }

    public function combogrid(Request $request)
    {
        $query = SavingType::where('is_active', 1);
        if ($request->is_employee == 0) 
        {
            $query = $query->where('is_employee', 0);
            $query = $query->where('department_id', $request->department_id);
        } else {
            $query = $query->where('is_employee', 1);
        }
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($filter).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function ($model) {
            $model['department_id'] = $model->department_id;
            $model['department'] = $model->getDepartment->name;
            return $model->only(['id','department_id','name','department']);
        });
        return $result;
    }

    public function combobox(Request $request)
    {
        $query = SavingType::select(
                    'finance.saving_types.id',
                    'finance.saving_types.department_id',
                    'finance.saving_types.name', 
                )
                ->where('finance.saving_types.is_active', 1);
        if ($request->is_employee == 0) 
        {
            $query = $query->where('finance.saving_types.is_employee', 0);
            $query = $query->where('finance.saving_types.department_id', $request->department_id);
        } else {
            $query = $query->where('finance.saving_types.is_employee', 1);
        }
        $query = $query->get();
        // result
        $result = array();
        foreach ($query as $val) 
        {
            $result[] = array(
                'id' => $val->id,
                'text' => $val->name .' / '. $val->getDepartment->name
            );
        }
        return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'is_employee' => $request->is_employee, 
				'name' => $request->name, 
				'cash_account' => $request->cash_account, 
				'credit_account' => $request->credit_account, 
                'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = SavingType::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'is_employee' => $query->is_employee, 
				'name' => $query->name, 
				'cash_account' => $query->cash_account, 
				'credit_account' => $query->credit_account, 
                'is_active' => $query->is_active, 
			);
			$after = array(
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'is_employee' => $request->has('is_employee') ? $request->is_employee : $query->is_employee, 
				'name' => $request->has('name') ? $request->name : $query->name, 
				'cash_account' => $request->has('cash_account') ? $request->cash_account : $query->cash_account, 
				'credit_account' => $request->has('credit_account') ? $request->credit_account : $query->credit_account, 
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