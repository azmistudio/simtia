<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Students;
use Carbon\Carbon;

class StudentEloquent implements StudentRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

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
        return Students::where('id', $payload['id'])->update($payload);
	}

	public function showIn($params)
	{
       	return Students::whereIn('id', $params)->get()->map(function ($model) {
                    $model['admission'] = $model->getProspectiveGroup->getAdmission->name;
                    $model['department'] = $model->getProspectiveGroup->getAdmission->getDepartment->name;
                    $model['class'] = $model->getClass->class;
                    $model['school_year'] = $model->getClass->getSchoolYear->school_year;
                    $model['grade'] = $model->getClass->getGrade->grade;
                    $model['gender'] = $this->getGender()[$model->gender];
                    $model['is_active'] = $this->getActive()[$model->is_active];
                    return $model;
                });
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Students::where('is_active', 1)->where('alumni', 0);
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getClass.getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // filter
        $nis = isset($request->params['fnis']) ? $request->params['fnis'] : '';
        if ($nis != '') 
        {
            $query = $query->where('student_no', 'like', '%'.Str::lower($nis).'%');
        }
        $class = isset($request->params['fclass']) ? $request->params['fclass'] : '';
        if ($class != '') 
        {
            $query = $query->where('class_id', $class);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->where('name', 'like', '%'.Str::lower($name).'%');
        }
        $fprospect_group = isset($request->fprospect_group) ? $request->fprospect_group : '';
        if ($fprospect_group != '') 
        {
            $query = $query->where('prospect_student_group_id', $fprospect_group);
        }
        $fclass = isset($request->fclass) ? $request->fclass : '';
        if ($fclass != '')
        {
            $query = $query->where('class_id', $fclass);
        }
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '' && $fdept != 1)
        {
            $query = $query->whereHas('getClass.getGrade', function($qry) use ($fdept) {
            	$qry->where('department_id', $fdept);
            });
        }
        $fgender = isset($request->fgender) ? $request->fgender : '';
        if ($fgender != '')
        {
            $query = $query->where('gender', $fgender);
        }
        $filters = $request->has('filterRules') ? json_decode($request->filterRules) : [];
        if (count($filters) > 0)
        {
            foreach ($filters as $val) 
            {
                $query = $query->where($val->field, 'like', '%'.Str::lower($val->value).'%');
            }
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getClass->getGrade->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['department_id'] = $model->getClass->getGrade->department_id;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['class'] = $model->getClass->class;
            $model['gender_name'] = $model->gender == 1 ? 'Ikhwan' : 'Akhwat';
            return $model;
        });
        return $result;
	}

	public function list(Request $request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = Students::where('is_active',1);
        $fclass = isset($request->fclass) ? $request->fclass : '';
        if ($fclass != '') 
        {
            $query = $query->where('class_id', $fclass);
        }
        $filters = $request->has('filterRules') ? json_decode($request->filterRules) : [];
        if (count($filters) > 0)
        {
            foreach ($filters as $val) 
            {
                $query = $query->where($val->field, 'like', '%'.Str::lower($val->value).'%');
            }
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['class'] = $model->getClass->class;
            $model['present'] = 0;
            $model['permit'] = 0;
            $model['sick'] = 0;
            $model['absent'] = 0;
            $model['leave'] = 0;
            $model['score'] = '';
            $model['remark'] = '';
            $model['student_num'] = '';
            $model['presence'] = 'Hadir';
            $model['memorize_id'] = 0;
            return $model;
        });
        return $result;
    }

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Students::destroy($id);
	}
	
	public function comboGridPlacement(Request $request)
    {
        $param = $this->gridRequest($request,'asc','seq');
        $query = DB::table('academic.student_placements_view');
        if (auth()->user()->getDepartment->is_all != 1)
        {
        	$query = $query->where('department_id', auth()->user()->getDepartment->id);
        }
        $q = isset($request->q) ? $request->q : '';
        if ($q != '') 
        {
            $query = $query->whereRaw('LOWER(class) like ?', ['%'.Str::lower($q).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    public function comboGrid(Request $request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = Students::where('is_active', 1)->where('alumni', 0);
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getClass.getSchoolYear', function($qry){
                $qry->where('department_id', auth()->user()->getDepartment->id);
            });
        }
        $q = isset($request->q) ? $request->q : '';
        if ($q != '') 
        {
            $query = $query->where('student_no', 'like', '%'.Str::lower($q).'%')->orWhereRaw('LOWER(name) like ?', ['%'.Str::lower($q).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getClass->getGrade->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['class'] = $model->getClass->class;
            $model['department_id'] = $model->getClass->getGrade->department_id;
            $model['schoolyear_id'] = $model->getClass->schoolyear_id;
            $model['grade_id'] = $model->getClass->grade_id;
            $model['student'] = $model->name;
            return $model->only(['id','department','grade','school_year','class','student_no','student','class_id','department_id','schoolyear_id','grade_id']);
        });
        return $result;
    }

    public function dataRoom(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'student_no');
        $query = Students::where('is_active', 1)
                    ->where('alumni', 0)
                    ->whereHas('getProspectiveStudent', function($qry) {
                        $qry->where('student_status',8);
                    })
                    ->whereRaw('academic.students.id NOT IN (SELECT academic.room_placements.student_id FROM academic.room_placements)');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getClass.getGrade', function($qry){
                $qry->where('department_id', auth()->user()->getDepartment->id);
            });
        }
        $fdepartment = isset($request->params['fdepartment']) ? $request->params['fdepartment'] : '';
        if ($fdepartment != '' && $fdepartment > 1) 
        {
            $query = $query->whereHas('getClass.getGrade', function($qry) use ($fdepartment){
                $qry->where('department_id', $fdepartment);
            });
        }
        $fgender = isset($request->params['fgender']) ? $request->params['fgender'] : '';
        if ($fgender != '') 
        {
            $query = $query->where('gender', $fgender);
        }
        $fnis = isset($request->params['fnis']) ? $request->params['fnis'] : '';
        if ($fnis != '') 
        {
            $query = $query->whereRaw('LOWER(student_no) LIKE ?', ['%'.Str::lower($fnis).'%']);
        }
        $fname = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($fname != '') 
        {
            $query = $query->whereRaw('LOWER(name) LIKE ?', ['%'.Str::lower($fname).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getClass->getGrade->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['class'] = $model->getClass->class;
            $model['department_id'] = $model->getClass->getGrade->department_id;
            $model['schoolyear_id'] = $model->getClass->schoolyear_id;
            $model['grade_id'] = $model->getClass->grade_id;
            $model['student'] = $model->name;
            $model['gender_name'] = $model->gender == 1 ? 'Ikhwan' : 'Akhwat';
            return $model;
        });
        return $result;
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		$query = Students::find($model_id);
		$before = array(
			'student_no' => $query->student_no, 
			'name' => $query->name, 
			'prospect_student_group_id' => $query->prospect_student_group_id, 
			'student_status' => $query->student_status, 
			'year_entry' => $query->year_entry, 
			'class_id' => $query->class_id, 
			'gender' => $query->gender, 
			'prospect_student_id' => $query->prospect_student_id, 
		);
		$after = array(
			'student_no' => $request->has('student_no') ? $request->student_no : $query->student_no, 
			'name' => $request->has('name') ? $request->name : $query->name, 
			'prospect_student_group_id' => $request->has('prospect_student_group_id') ? $request->prospect_student_group_id : $query->prospect_student_group_id, 
			'student_status' => $request->has('student_status') ? $request->student_status : $query->student_status, 
			'year_entry' => $request->has('year_entry') ? $request->year_entry : $query->year_entry, 
			'class_id' => $request->has('class_id') ? $request->class_id : $query->class_id, 
			'gender' => $request->has('gender') ? $request->gender : $query->gender, 
			'prospect_student_id' => $request->has('prospect_student_id') ? $request->prospect_student_id : $query->prospect_student_id, 
		);
		if ($action == 'Ubah')
		{
	        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
		} else {
	        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
		}
	}
}