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
use Modules\Academic\Entities\AdmissionProspectGroup;
use Modules\Academic\Entities\AdmissionProspectGroupView;
use Carbon\Carbon;

class ProspectGroupEloquent implements ProspectGroupRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return AdmissionProspectGroup::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['occupied','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return AdmissionProspectGroup::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
	{
		return AdmissionProspectGroup::whereIn('id', $params)
                ->groupBy('id','admission_id','group','capacity','remark')
                ->get()->map(function ($model) {
                    $model['department'] = $model->getAdmission->getDepartment->name;
                    $model['occupied'] = count($model->getAdmissionProspect);
                    $model['admission_id'] = $model->getAdmission->name;
                    return $model->only(['id','admission_id','group','capacity','remark','department','occupied']);
                });
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = AdmissionProspectGroup::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->whereHas('getAdmission.getDepartment', function($qry) {
				$qry->where('department_id', auth()->user()->department_id);
			});
		} 
        $query = $query->groupBy('id','group','admission_id','capacity');
        // filter
        $department_id = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($department_id != '')
        {
            $query = $query->whereHas('getAdmission.getDepartment', function($qry) use($department_id) {
				$qry->where('department_id', $department_id);
			});
        }
        $group = isset($request->params['fgroup']) ? $request->params['fgroup'] : '';
        if ($group != '') 
        {
            $query = $query->where('group', 'like', '%'.Str::lower($group).'%');
        }
        // result
        $result["total"] = $query->distinct()->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['capacity'] = $model->capacity .'/'. count($model->getAdmissionProspect);
            $model['admission_id'] = $model->getAdmission->name;
            $model['department'] = $model->getAdmission->getDepartment->name;
            return $model->only(['id','group','admission_id','department','capacity']);
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return AdmissionProspectGroup::destroy($id);
	}

	public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request);
        // query
        $query = AdmissionProspectGroupView::select('*',DB::raw("id as adm_id"),DB::raw('admission_id as admission'))->where('is_active',1);
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		} 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->where('group', 'like', '%'.$filter.'%');
        }
        $department_id = $request->has('department_id') ? $request->department_id : '';
        if ($department_id != '')
        {
            $query = $query->where('department_id', $department_id);
        }
        $is_active = $request->has('is_active') ? $request->is_active : '';
        if ($is_active != '')
        {
            $query = $query->where('is_active', $is_active);
        }
        $result["total"] = $query->count();
        $result["rows"] =  $query->orderBy('id')->get();
        return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'group' => $request->group, 
				'admission_id' => $request->admission_id, 
				'capacity' => $request->capacity, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = AdmissionProspectGroup::find($model_id);
			$before = array(
				'group' => $query->group, 
				'admission_id' => $query->admission_id, 
				'capacity' => $query->capacity, 
			);
			$after = array(
				'group' => $request->has('group') ? $request->group : $query->group, 
				'admission_id' => $request->has('admission_id') ? $request->admission_id : $query->admission_id, 
				'capacity' => $request->has('capacity') ? $request->capacity : $query->capacity, 
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