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
use Modules\Academic\Entities\AdmissionProspect;
use Modules\Academic\Entities\ProspectStudentView;
use Carbon\Carbon;

class ProspectEloquent implements ProspectRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['columnopts','additional']);
		$payload['photo'] = $request->input('photo');
		$this->logActivity($request, 0, $subject, 'Tambah');
		return AdmissionProspect::create($payload);
	}

	public function update(Request $request, $subject)
	{
		if ($request->hasFile('photo')) 
		{
        	$payload = Arr::except($request->all(), ['admission_id','columnopts','additional','created_at','_token']);
		    $payload['photo'] = $request->input('photo');
		} else {
			$payload = Arr::except($request->all(), ['photo','admission_id','columnopts','additional','created_at','_token']);
		}
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        return AdmissionProspect::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
	{
       	return AdmissionProspect::whereIn('id', $params)->orderBy('registration_no')->get()->map(function ($model) {
		            $model['department_id'] = $model->getProspectGroup->getAdmission->getDepartment->id;
		            $model['department'] = $model->getProspectGroup->getAdmission->getDepartment->name;
		            $model['admission_name'] = $model->getProspectGroup->getAdmission->name;
		            $model['is_active'] = $this->getActive()[$model->is_active];
		            return $model;
		        });
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = AdmissionProspect::join('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
					   ->join('academic.admissions','academic.admissions.id','=','academic.prospect_student_groups.admission_id')
					   ->select('academic.prospect_students.*','academic.admissions.department_id')
					   ->where('academic.admissions.department_id', auth()->user()->getDepartment->id);
		} else {
	        $query = AdmissionProspect::select('*');
		}
        // filter
        $group = isset($request->params['fgroup']) ? $request->params['fgroup'] : '';
        if ($group != '') 
        {
            $query = $query->where('prospect_group_id', $group);
        }
        $fgroup = isset($request->fprospect_group) ? $request->fprospect_group : '';
        if ($fgroup != '') 
        {
            $query = $query->where('prospect_group_id', $fgroup);
        }
        $is_student = $request->is_student || isset($request->params['fstudent']) ?: '';
        if ($is_student != '') 
        {
            $query = $query->where('student_id', null)->orWhere('student_id', 0);
        }
        $register = isset($request->params['fregister']) ? $request->params['fregister'] : '';
        if ($register != '') 
        {
            $query = $query->where('registration_no', 'like', '%'.Str::lower($register).'%');
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->where('name', 'like', '%'.Str::lower($name).'%');
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['id_admission'] = $model->getProspectGroup->getAdmission->id;
            $model['admission_id'] = $model->getProspectGroup->getAdmission->name;
            $model['department_id'] = $model->getProspectGroup->getAdmission->getDepartment->id;
            return $model;
        });
        return $result;
	}	

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return AdmissionProspect::destroy($id);
	}

	public function dataView(Request $request)
    {
        $param = $this->gridRequest($request);
        // query
        $query = ProspectStudentView::select('*');
        // filter
        $register = isset($request->params['fregister']) ? $request->params['fregister'] : '';
        if ($register != '') 
        {
            $query = $query->where('registration_no', 'like', '%'.Str::lower($register).'%');
        }
        $group = isset($request->fgroup) ? $request->fgroup : '';
        if ($group != '') 
        {
            $query = $query->where('prospect_group_id', $group);
        }
        $gender = isset($request->params['fgender']) ? $request->params['fgender'] : '';
        if ($gender != '') 
        {
            $query = $query->where('gender', $gender);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->where('name', 'like', '%'.Str::lower($name).'%');
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
        	$model['name'] = Str::upper($model->name);
        	$model['group'] = Str::upper($model->group);
        	$model['gender'] = $this->getGender()[$model->gender];
        	return $model;
        });
        return $result;
    }

    public function combogrid(Request $request)
    {
        $param = $this->gridRequest($request,'asc');
        $query = AdmissionProspect::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->whereHas('getProspectGroup.getAdmission', function($qry) {
				$qry->where('department_id', auth()->user()->department_id);
			});
		}
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($filter).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
        	$model['department_id'] = $model->getProspectGroup->getAdmission->department_id;
        	$model['department'] = $model->getProspectGroup->getAdmission->getDepartment->name;
        	$model['admission_id'] = $model->getProspectGroup->admission_id;
        	$model['admission'] = $model->getProspectGroup->getAdmission->name;
        	$model['group'] = $model->getProspectGroup->group;
        	return $model->only(['id','registration_no','name','prospect_group_id','department_id','department','admission_id','admission','group']);
        });
        return $result;
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'student_no' => $request->student_no, 
				'name' => $request->name, 
				'prospect_group_id' => $request->prospect_group_id, 
				'student_status' => $request->student_status, 
				'mobile' => $request->mobile, 
				'email' => $request->email, 
				'father' => $request->father, 
				'mother' => $request->mother, 
				'father_mobile' => $request->father_mobile, 
				'mother_mobile' => $request->mother_mobile, 
				'parent_guardian' => $request->parent_guardian, 
				'father_email' => $request->father_email, 
				'mother_email' => $request->mother_email, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = AdmissionProspect::find($model_id);
			$before = array(
				'student_no' => $query->student_no, 
				'name' => $query->name, 
				'prospect_group_id' => $query->prospect_group_id, 
				'student_status' => $query->student_status, 
				'mobile' => $query->mobile, 
				'email' => $query->email, 
				'father' => $query->father, 
				'mother' => $query->mother, 
				'father_mobile' => $query->father_mobile, 
				'mother_mobile' => $query->mother_mobile, 
				'parent_guardian' => $query->parent_guardian, 
				'father_email' => $query->father_email, 
				'mother_email' => $query->mother_email, 
			);
			$after = array(
				'student_no' => $request->has('student_no') ? $request->student_no : $query->student_no, 
				'name' => $request->has('name') ? $request->name : $query->name, 
				'prospect_group_id' => $request->has('prospect_group_id') ? $request->prospect_group_id : $query->prospect_group_id, 
				'student_status' => $request->has('student_status') ? $request->student_status : $query->student_status, 
				'mobile' => $request->has('mobile') ? $request->mobile : $query->mobile, 
				'email' => $request->has('email') ? $request->email : $query->email, 
				'father' => $request->has('father') ? $request->father : $query->father, 
				'mother' => $request->has('mother') ? $request->mother : $query->mother, 
				'father_mobile' => $request->has('father_mobile') ? $request->father_mobile : $query->father_mobile, 
				'mother_mobile' => $request->has('mother_mobile') ? $request->mother_mobile : $query->mother_mobile, 
				'parent_guardian' => $request->has('parent_guardian') ? $request->parent_guardian : $query->parent_guardian, 
				'father_email' => $request->has('father_email') ? $request->father_email : $query->father_email, 
				'mother_email' => $request->has('mother_email') ? $request->mother_email : $query->mother_email, 
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