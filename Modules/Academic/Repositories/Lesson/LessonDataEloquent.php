<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Lesson;
use Carbon\Carbon;

class LessonDataEloquent implements LessonDataRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Lesson::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return Lesson::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
	{
        return Lesson::whereIn('id', $params)->orderBy('id')->get()->map(function ($model) {
		            $model['mandatory'] = $this->getMandatory()[$model->mandatory];
		            $model['is_active'] = $this->getActive()[$model->is_active];
		            return $model;
		        });
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Lesson::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->getDepartment->id);
		} 
        // filter
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '') 
        {
            $query = $query->where('department_id', $fdept);
        }
        $code = isset($request->params['fcode']) ? $request->params['fcode'] : '';
        if ($code != '') 
        {
            $query = $query->whereRaw('LOWER(code) like ?', ['%'.Str::lower($code).'%']);
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
            return $model->only(['id','department_id','code','name']);
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Lesson::destroy($id);
	}

	public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request);
        $query = Lesson::where('is_active', 1);
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(code) like ?', ['%'.Str::lower($filter).'%']);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function ($model) {
            $model['department'] = $model->getDepartment->name;
            return $model;
        });
        return $result;
    }

    public function combogridTeacher(Request $request)
    {
        $param = $this->gridRequest($request);
        $query = Lesson::select('*');
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(code) like ?', ['%'.$filter.'%']);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy('id')->get()->map(function ($model) {
            $model['teacher'] = $model->getTeacher->getEmployee->name;
            $model['status'] = $model->getStatus->name;
            $model['teachers_id'] = $model->getTeacher->id;
            $model['status_id'] = $model->getTeacher->status_id;
            $model['employee_id'] = $model->getTeacher->employee_id;
            return $model->only(['id','code','name','department_id','teacher','status','teachers_id','status_id','employee_id']);
        });
        return $result;
    }

    public function combobox($id)
    {
        return Lesson::selectRaw('id, UPPER(name) AS text')->where('department_id', $id)->where('is_active', 1)->orderBy('id')->get();
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'code' => $request->code, 
				'name' => $request->name, 
				'department_id' => $request->department_id, 
				'group_id' => $request->group_id, 
				'mandatory' => $request->mandatory, 
				'is_active' => $request->is_active, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Lesson::find($model_id);
			$before = array(
				'code' => $query->code, 
				'name' => $query->name, 
				'department_id' => $query->department_id, 
				'group_id' => $query->group_id, 
				'mandatory' => $query->mandatory, 
				'is_active' => $query->is_active, 
			);
			$after = array(
				'code' => $request->has('code') ? $request->code : $query->code, 
				'name' => $request->has('name') ? $request->name : $query->name, 
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'group_id' => $request->has('group_id') ? $request->group_id : $query->group_id, 
				'mandatory' => $request->has('mandatory') ? $request->mandatory : $query->mandatory, 
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