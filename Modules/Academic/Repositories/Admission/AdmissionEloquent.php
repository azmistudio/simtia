<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Admission;
use Carbon\Carbon;

class AdmissionEloquent implements AdmissionRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		$query = Admission::create($payload);
		return Admission::where('id','<>',$query->id)->where('department_id',$payload['department_id'])->update(['is_active' => 2]);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        $updated = Admission::where('id', $payload['id'])->first();
        Admission::where('id', $payload['id'])->update($payload);
        if ($updated->is_active <> $payload['is_active'])
        {
            if ($payload['is_active'] == 1)
            {
                Admission::where('id','<>',$payload['id'])->where('department_id', $payload['department_id'])->update(['is_active' => 2]);
            } else {
                $query = Admission::select('id')->where('id','<>',$payload['id'])->where('department_id', $payload['department_id'])->orderByDesc('id')->first();
                Admission::where('id',$payload['id'])->where('department_id', $payload['department_id'])->update(['is_active' => 1]);
            }
        }
        return;
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Admission::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        $query = $query->groupBy('id','department_id','name');
        // filter
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '') 
        {
            $query = $query->where('department_id', $fdept);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->distinct()->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            $total = 0;
            foreach ($model->getProspectiveGroup as $val) 
            {
                $total += count($val->getAdmissionProspect);
            }
            $model['total_admission'] = $total;
            return $model->only(['id','department_id','name','total_admission','is_active']);
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Admission::destroy($id);
	}

	public function combogrid(Request $request)
    {
    	$param = $this->gridRequest($request);
        $query = Admission::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->where('name', $filter);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            return $model->only(['id','department_id','name']);
        });
        return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'name' => $request->name, 
				'department_id' => $request->department_id, 
				'prefix' => $request->prefix, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Admission::find($model_id);
			$before = array(
				'name' => $query->name, 
				'department_id' => $query->department_id, 
				'prefix' => $query->prefix, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'prefix' => $request->has('prefix') ? $request->prefix : $query->prefix, 
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