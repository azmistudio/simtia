<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Columns;
use Carbon\Carbon;

class ColumnEloquent implements ColumnRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Columns::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        return Columns::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Columns::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        // filter
        $dept = isset($request->params['fdepartment']) ? $request->params['fdepartment'] : '';
        if ($dept != '') 
        {
            $query = $query->where('department_id', $dept);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            $model['type'] = $this->getColumnOption()[$model->type];
            return $model->only(['id','department_id','name','type']);
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Columns::destroy($id);
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'name' => $request->name, 
				'type' => $request->type, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Columns::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'name' => $query->name, 
				'type' => $query->type, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'name' => $request->has('name') ? $request->name : $query->name, 
				'type' => $request->has('type') ? $request->type : $query->type, 
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