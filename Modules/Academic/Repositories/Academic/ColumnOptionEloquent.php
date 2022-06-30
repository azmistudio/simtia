<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\ColumnOption;
use Carbon\Carbon;

class ColumnOptionEloquent implements ColumnOptionRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return ColumnOption::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        return ColumnOption::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request, $id)
	{
		$param = $this->gridRequest($request);
        $query = ColumnOption::where('column_id', $id);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) {
        	$model['is_active'] = $this->getActive()[$model->is_active];
        	return $model;
        });
        return $result;
	}	

	public function destroy($request, $subject)
	{
		$this->logActivity($request, $request->id, $subject, 'Hapus');
		return ColumnOption::destroy($request->id);
	}

	public function datalist($id)
    {
        return ColumnOption::selectRaw('INITCAP(name) AS text')->where('column_id', $id)->where('is_active', 1)->orderBy('order')->get();
    }

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'column_id' => $request->column_id, 
				'name' => $request->name, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = ColumnOption::find($model_id);
			$before = array(
				'column_id' => $query->column_id, 
				'name' => $query->name, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'column_id' => $request->has('column_id') ? $request->column_id : $query->column_id, 
				'name' => $request->has('name') ? $request->name : $query->name, 
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