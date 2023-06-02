<?php

namespace Modules\Academic\Repositories\Academic;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Generation;
use Modules\Academic\Entities\Grade;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Semester;
use Modules\Academic\Entities\Classes;
use Modules\Academic\Entities\Students;
use Carbon\Carbon;
use Exception;

class AcademicEloquent implements AcademicRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	// grade
	public function createGrade(Request $request, $subject)
	{
		$lastOrder = Grade::select('order')->orderByDesc('id')->limit(1)->first();
        $newOrder = $lastOrder != null ? intval($lastOrder->order) + 1 : 1;
		//
		$payload = $request->all();
		$payload['order'] = $newOrder;
		$this->logActivity($request, 0, $subject, 'Tambah', 'grade');
		return Grade::create($payload);
	}

	public function updateGrade(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah', 'grade');
        return Grade::where('id', $request->id)->update($payload);
	}

	public function dataGrade(Request $request)
	{
		$param = $this->gridRequest($request);
		$query = Grade::select('*');
		if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            return $model->only(['id','department_id','grade','remark']);
        });
        return $result;
	}

	public function destroyGrade($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus', 'grade');
		return Grade::destroy($id);
	}

	public function comboboxGrade($id)
    {
        return Grade::selectRaw('id, UPPER(grade) AS text')->where('department_id', $id)->orderBy('id')->get();
    }

    public function combogridGrade(Request $request)
    {
    	$param = $this->gridRequest($request);
    	$query = Grade::select('*');
	    if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->whereHas('getDepartment', function($qry) {
				$qry->where('department_id', auth()->user()->department_id);
			});
		}
        if ($request->has('department_id'))
        {
            $query = $query->whereHas('getDepartment', function($qry) use ($request) {
				$qry->where('department_id', $request->department_id);
			});
        }
	    //
    	$filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->whereHas('getDepartment', function($qry) use($filter) {
            	$qry->whereRaw('LOWER(name) like ?', ['%'.Str::lower($filter).'%']);
            });
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function ($model) {
			                    $model['school_year'] = $model->getSchoolYearByDept(1)->school_year;
			                    $model['schoolyear_id'] = $model->getSchoolYearByDept(1)->id;
			                    $model['semester'] = isset($model->getSemesterByDept) ? Str::upper($model->getSemesterByDept->semester) : '-';
			                    $model['semester_id'] = isset($model->getSemesterByDept) ? $model->getSemesterByDept->id : 0;
			                    $model['department'] = $model->getDepartment->name;
			                    return $model->only(['id','department','grade','school_year','schoolyear_id','semester','semester_id','department_id']);
			                });
        return $result;
    }

	// school year
	public function createSchoolYear(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah', 'schoolyear');
		return SchoolYear::create($payload);
	}

	public function updateSchoolYear(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah', 'schoolyear');
        SchoolYear::where('id', $request->id)->update($payload);
        //
        $updated = SchoolYear::find($request->id);
        if ($updated->is_active <> $request->is_active)
        {
        	if ($request->is_active == 1)
            {
                SchoolYear::where('id','<>',$request->id)->where('department_id', $request->department_id)->update(['is_active' => 2]);
            } else {
                $query = SchoolYear::select('id')->where('id','<>',$request->id)->where('department_id', $request->department_id)->orderByDesc('id')->first();
                SchoolYear::where('id',$query->id)->where('department_id', $request->department_id)->update(['is_active' => 1]);
            }
        }
        return;
	}

	public function dataSchoolYear(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = SchoolYear::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            $model['school_year'] = $model->is_active == 1 ? $model->school_year . ' (A)' : $model->school_year;
            return $model->only(['id','department_id','school_year','is_active']);
        });
        return $result;
	}

	public function destroySchoolYear($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus', 'schoolyear');
		return SchoolYear::destroy($id);
	}

	public function comboboxSchoolYear($id)
    {
        return SchoolYear::take(5)->orderByDesc('id')->get()->map(function ($model){
        	$model['text'] = $model->is_active == 1 ? $model->school_year . ' (Aktif)' : $model->school_year;
        	return $model->only(['id','text']);
        });
    }

    public function combogridSchoolYear(Request $request)
    {
        $param = $this->gridRequest($request);
        $query = SchoolYear::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->where('school_year', 'like', '%'.$filter.'%');
        }
        $is_active = isset($request->is_active) ? $request->is_active : '';
        if ($is_active != '')
        {
            $query = $query->where('is_active', $is_active);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
        	$model['department'] = $model->getDepartment->name;
        	$model['school_year'] = explode('/', $model->school_year)[0];
        	return $model;
        });
        return $result;
    }

	// semester
	public function createSemester(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah', 'semester');
		return Semester::create($payload);
	}

	public function updateSemester(Request $request, $subject)
	{
        // validate active semester
        $old_status = Semester::find($request->id)->is_active;
        if ($request->is_active == 1 && $old_status == 2)
        {
            $total_semester = Semester::where('department_id',$request->department_id)->count();
            $total_active = Semester::where('department_id',$request->department_id)->where('is_active',1)->count();
            if ($total_active >= ($total_semester / 2))
            {
                throw new Exception('Jumlah semester aktif maksimal ' . $total_semester / 2 . ' semester.', 1);
            }
        }
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah', 'semester');
        Semester::where('id', $request->id)->update($payload);
	}

	public function dataSemester(Request $request)
	{
		$param = $this->gridRequest($request, 'asc');
        $query = Semester::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            return $model->only(['id','department_id','semester','is_active']);
        });
        return $result;
	}

	public function destroySemester($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus', 'semester');
		return Semester::destroy($id);
	}

	public function comboboxSemester($id)
    {
        return Semester::selectRaw('id, UPPER(semester) AS text')->where('department_id', $id)->orderBy('id')->get();
    }

    public function combogridSemester(Request $request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = Semester::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        //
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getDepartment->name;
            $model['semester'] = $model->is_active == 1 ? $model->semester .' (A)' : $model->semester;
            return $model;
        });
        return $result;
    }

    public function reportAssessmentSemester($dept_id, $lesson_id, $student_id)
    {
        return Semester::where('department_id', $dept_id)
                ->whereHas('getExam', function ($query) use ($lesson_id) {
                    $query->where('lesson_id', $lesson_id);
                })
                ->whereHas('getExam.getExamScore', function ($query) use ($student_id) {
                    $query->where('student_id', $student_id);
                })
                ->groupBy('id','semester')
                ->get()->map(function ($model) {
                    return $model->only(['id','semester']);
                });
    }

	// class
	public function createClass(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah', 'class');
		return Classes::create($payload);
	}

	public function updateClass(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['department_id','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah', 'class');
        return Classes::where('id', $request->id)->update($payload);
	}

	public function showClassIn($params)
	{
		return Classes::whereIn('id', $params)
				->groupBy('id','grade_id','class','capacity')
				->orderBy('grade_id')
                ->get()->map(function ($model) {
                    $model['schoolyear_id'] = $model->getSchoolYear->school_year;
                    $model['grade_id'] = $model->getGrade->grade;
                    $model['occupied'] = count($model->getStudents);
                    $model['department'] = $model->getGrade->getDepartment->name;
                    $model['employee_id'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
                    $model['is_active'] = $this->getActive()[$model->is_active];
                    return $model->only(['id','schoolyear_id','grade_id','class','capacity','occupied','department','remark','is_active','employee_id']);
                });
	}

	public function dataClass(Request $request)
	{
        $param = $this->gridRequest($request);
        $query = Classes::groupBy('id','grade_id','class','capacity');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->whereHas('getGrade.getDepartment', function($qry) {
				$qry->where('department_id', auth()->user()->department_id);
			});
		}
        // result
        $result["total"] = $query->distinct()->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['id_department'] = $model->getGrade->department_id;
            $model['department_id'] = $model->getGrade->getDepartment->name;
            $model['schoolyear_id'] = $model->getSchoolYear->is_active == 1 ? '<b>'.$model->getSchoolYear->school_year.'</b>' : $model->getSchoolYear->school_year;
            $model['id_schoolyear'] = $model->schoolyear_id;
            $model['id_grade'] = $model->grade_id;
            $model['grade_id'] = $model->getGrade->grade;
            $model['occupied'] = count($model->getStudents);
            $model['quota'] = $model->capacity .'/'. count($model->getStudents);
            return $model->only(['id','id_department','department_id','school_year','schoolyear_id','id_grade','grade_id','class','capacity','occupied','quota']);
        });
        return $result;
	}

	public function destroyClass($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus', 'class');
		Classes::destroy($id);
	}

	public function quotaClass($id)
    {
        return Students::where('class_id', $id)
        		->select(
                    'academic.classes.capacity',
                    DB::raw('COUNT(class_id) AS occupied'),
                )
                ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                ->groupBy('academic.classes.capacity')
                ->first();
    }

    public function combogridClass(Request $request)
    {
        $param = $this->gridRequest($request);
        // query
        $join_sub = Students::select('class_id')->where('is_active', 1);
        $join_sub_sem = Semester::select('id','department_id','semester','grade_id')->where('is_active', 1);
        $query = Classes::select(
			            'academic.classes.id',
			            'schoolyear_id',
			            'academic.classes.grade_id',
			            'class',
			            DB::raw('departments.id as department_id'),
			            DB::raw('schoolyear_id as school_year'),
			            DB::raw('UPPER(departments.name) as department'),
			            DB::raw("capacity || '/' || COUNT(students.class_id) as capacity"),
			            DB::raw('COUNT(students.class_id) as occupied'),
			            DB::raw('UPPER(semesters.semester) as semester'),
			            DB::raw('semesters.id as semester_id'),
			            'academic.schoolyears.start_date',
			            'academic.schoolyears.end_date',
			        )->leftJoinSub($join_sub, 'students', function ($join) {
			            $join->on('academic.classes.id', '=', 'students.class_id');
			        })
			        ->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
			        ->join('academic.grades','academic.grades.id','=','academic.classes.grade_id')
			        ->join('departments','departments.id','=','academic.grades.department_id')
			        ->joinSub($join_sub_sem, 'semesters', function ($join) {
			            $join->on('classes.grade_id', '=', 'semesters.grade_id');
			        })
			        ->where('academic.classes.is_active', 1)
			        ->groupBy('academic.classes.id','schoolyear_id','academic.classes.grade_id','class','departments.id','departments.name','semesters.semester','academic.schoolyears.start_date','academic.schoolyears.end_date', 'semesters.id');

        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->whereHas('getGrade.getDepartment', function($qry) {
				$qry->where('department_id', auth()->user()->department_id);
			});
		}
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->where('class', $filter);
        }
        $fclass_not = isset($request->fclass_not) ? $request->fclass_not : '';
        $fschoolyear_not = isset($request->fschoolyear_not) ? $request->fschoolyear_not : '';
        $fgrade_not = isset($request->fgrade_not) ? $request->fgrade_not : '';
        if ($fclass_not != '' && $fschoolyear_not != '' && $fgrade_not != '')
        {
            $schoolyear = SchoolYear::select('end_date')->where('id', $fschoolyear_not)->first();
            $schoolyears = SchoolYear::select('id')->whereDate('start_date','>=',$schoolyear->end_date)->get();
            $schoolyears_params = array();
            if (!empty($schoolyears))
            {
                foreach ($schoolyears as $schoolyear)
                {
                    $schoolyears_params[] = $schoolyear->id;
                }
                $query = $query->where('academic.classes.id', '<>', $fclass_not)->where('academic.classes.grade_id', '<>', $fgrade_not)->whereIn('academic.classes.schoolyear_id', $schoolyears_params);
            } else {
                $query = $query->where('academic.classes.id', 0);
            }
        }
        $fclass_yes = isset($request->fclass_yes) ? $request->fclass_yes : '';
        $fschoolyear_yes = isset($request->fschoolyear_yes) ? $request->fschoolyear_yes : '';
        $fgrade_yes = isset($request->fgrade_yes) ? $request->fgrade_yes : '';
        if ($fclass_yes != '' && $fschoolyear_yes != '' && $fgrade_yes != '')
        {
            $schoolyear = SchoolYear::select('end_date')->where('id', $fschoolyear_yes)->first();
            $schoolyears = SchoolYear::select('id')->whereDate('start_date','>',$schoolyear->end_date)->get();
            $schoolyears_params = array();
            if (!empty($schoolyears))
            {
                foreach ($schoolyears as $schoolyear)
                {
                    $schoolyears_params[] = $schoolyear->id;
                }
                $query = $query->where('academic.classes.grade_id', $fgrade_yes)->whereIn('academic.classes.schoolyear_id', $schoolyears_params);
            } else {
                $query = $query->where('academic.classes.id', 0);
            }
        }
        $fdept_not = isset($request->fdept_not) ? $request->fdept_not : '';
        if ($fdept_not != '' && $fschoolyear_not != '')
        {
            $schoolyear = SchoolYear::select('end_date')->where('id', $fschoolyear_not)->first();
            $schoolyears = SchoolYear::select('id')->whereDate('start_date','>',$schoolyear->end_date)->get();
            $schoolyears_params = array();
            if (!empty($schoolyears))
            {
                foreach ($schoolyears as $schoolyear)
                {
                    $schoolyears_params[] = $schoolyear->id;
                }
                $query = $query->whereIn('academic.classes.schoolyear_id', $schoolyears_params);
            } else {
                $query = $query->where('academic.classes.id', 0);
            }
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function ($model) {
            $model['schoolyear_id'] = $model->getSchoolYear->school_year;
            $model['grade'] = $model->getGrade->grade;
            return $model;
        });
        return $result;
    }

    public function comboGridClassView(Request $request)
    {
        $param = $this->gridRequest($request);
        // query
        $query = DB::table('academic.class_view');
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query->where('department_id', auth()->user()->department_id);
		}
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->where('class', $filter);
        }
        $fdept_not = isset($request->fdept_not) ? $request->fdept_not : '';
        if ($fdept_not != '')
        {
        	$query = $query->where('department_id','<>',$fdept_not);
        }
        $fscy_not = isset($request->fscy_not) ? $request->fscy_not : '';
        if ($fscy_not != '')
        {
        	$query = $query->where('schoolyear_id','<>',$fscy_not);
        }
        $fsemester = isset($request->fsemester) ? $request->fsemester : '';
        if ($fsemester != '')
        {
        	$query = $query->where('semester', $fsemester);
        }
        $fgrade = isset($request->fgrade) ? $request->fgrade : '';
        if ($fgrade != '')
        {
        	$query = $query->where('grade', $fgrade);
        }
        $fstu_active = isset($request->fstu_active) ? $request->fstu_active : '';
        if ($fstu_active != '')
        {
        	$query = $query->where('stu_active', $fstu_active);
        }
        $fsem_active = isset($request->fsem_active) ? $request->fsem_active : '';
        if ($fsem_active != '')
        {
        	$query = $query->where('sem_active', $fsem_active);
        }
        $fscy_active = isset($request->fscy_active) ? $request->fscy_active : '';
        if ($fscy_active != '')
        {
        	$query = $query->where('scy_active', $fscy_active);
        }
        $fcount = isset($request->fcount) ? $request->fcount : 0;
        if ($fcount == 1)
        {
        	$query = $query->where('occupied', '>', 0);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function($model){
        	$model->grade = Str::upper($model->grade);
        	$model->class = Str::upper($model->class);
        	return $model;
        });
        return $result;
    }

    public function comboGridClassPlacement(Request $request)
    {
        $param = $this->gridRequest($request);
        // query
        $query = Classes::select('*')
                    ->whereHas('getSchoolYear', function ($query) {
                        $query->where('is_active', 1);
                    })
                    ->groupBy('id','schoolyear_id','grade_id','class');
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->where('class', $filter);
        }
        $department_id = $request->has('department_id') ? $request->department_id : '';
        if ($request->is_filter == 0 && $department_id != '')
        {
            $query = $query->whereHas('getGrade', function ($query) use ($department_id) {
                            $query->where('department_id', $department_id);
                        });
        } else {
            $query = $query->whereHas('getGrade', function ($query) {
                            $query->where('department_id', 0);
                        });
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy('id')->get()->map(function ($model) {
            $model['schoolyear_id'] = $model->getSchoolYear->school_year;
            $model['grade_id'] = $model->getGrade->grade;
            $model['grade'] = $model->getGrade->id;
            $model['school_year'] = $model->getSchoolYear->id;
            $model['capacity'] = $model->capacity .'/'. count($model->getStudents);
            return $model->only(['id','schoolyear_id','grade_id','class','grade','school_year','capacity']);
        });
        return $result;
    }

    public function comboGridClassStudent(Request $request)
    {
        // request
        $param = $this->gridRequest($request);
        $query = DB::table('academic.class_students_view')->where('is_active',1);
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->where('department_id', auth()->user()->department_id);
		}
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->where('class', $filter);
        }
        $department_id = $request->has('department_id') ? $request->department_id : '';
        if ($department_id != '')
        {
            $query = $query->where('department_id', $department_id);
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get();
        return $result;
    }

    public function comboboxClass($grade_id, $schoolyear_id)
    {
        return Classes::selectRaw('id, UPPER(class) AS text')
                ->where('grade_id', $grade_id)
                ->where('schoolyear_id', $schoolyear_id)
                ->where('is_active', 1)
                ->orderBy('id')->get();
    }

    public function comboGridClassOnly(Request $request)
    {
        // request
        $param = $this->gridRequest($request);
        $query = Classes::where('is_active',1);
        if (auth()->user()->getDepartment->is_all != 1)
		{
			$query = $query->whereHas('getGrade', function($qry) {
				$qry->where('department_id', auth()->user()->department_id);
			});
		}
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '')
        {
            $query = $query->where('class', $filter);
        }
        $department_id = $request->has('department_id') ? $request->department_id : '';
        if ($department_id != '')
        {
        	$query = $query->whereHas('getGrade', function($qry) use($department_id) {
				$qry->where('department_id', $department_id);
			});
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function($model){
        	$model['department'] = $model->getGrade->getDepartment->name;
        	$model['department_id'] = $model->getGrade->department_id;
        	$model['grade'] = $model->getGrade->grade;
        	$model['class'] = Str::upper($model['class']);
        	return $model;
        });
        return $result;
    }

	private function logActivity(Request $request, $model_id, $subject, $action, $entity)
	{
		if ($action == 'Tambah')
		{
			switch ($entity) {
				case 'schoolyear':
					$data = array(
						'school_year' => $request->school_year,
						'start_date' => $request->start_date,
						'end_date' => $request->end_date,
						'department_id' => $request->department_id,
						'is_active' => $request->is_active,
					);
					break;

				case 'semester':
					$data = array(
						'semester' => $request->semester,
						'department_id' => $request->department_id,
						'is_active' => $request->is_active,
					);
					break;

				case 'class':
					$data = array(
						'grade_id' => $request->grade_id,
						'schoolyear_id' => $request->schoolyear_id,
						'class' => $request->class,
						'employee_id' => $request->employee_id,
						'is_active' => $request->is_active,
					);
					break;

				default:
					$data = array(
						'grade' => $request->grade,
						'department_id' => $request->department_id,
						'is_active' => $request->is_active,
					);
					break;
			}
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			switch ($entity) {
				case 'schoolyear':
					$query = SchoolYear::find($model_id);
					$before = array(
						'school_year' => $query->school_year,
						'start_date' => $query->start_date,
						'end_date' => $query->end_date,
						'department_id' => $query->department_id,
						'is_active' => $query->is_active,
					);
					$after = array(
						'school_year' => $request->has('school_year') ? $request->school_year : $query->school_year,
						'start_date' => $request->has('start_date') ? $request->start_date : $query->start_date,
						'end_date' => $request->has('end_date') ? $request->end_date : $query->end_date,
						'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id,
						'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active,
					);
					break;

				case 'semester':
					$query = Semester::find($model_id);
					$before = array(
						'semester' => $query->semester,
						'department_id' => $query->department_id,
						'is_active' => $query->is_active,
					);
					$after = array(
						'semester' => $request->has('semester') ? $request->semester : $query->semester,
						'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id,
						'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active,
					);
					break;

				case 'class':
					$query = Classes::find($model_id);
					$before = array(
						'grade_id' => $query->grade_id,
						'schoolyear_id' => $query->schoolyear_id,
						'class' => $query->class,
						'employee_id' => $query->employee_id,
						'is_active' => $query->is_active,
					);
					$after = array(
						'grade_id' => $request->has('grade_id') ? $request->grade_id : $query->grade_id,
						'schoolyear_id' => $request->has('schoolyear_id') ? $request->schoolyear_id : $query->schoolyear_id,
						'class' => $request->has('class') ? $request->class : $query->class,
						'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id,
						'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active,
					);
					break;

				default:
					$query = Grade::find($model_id);
					$before = array(
						'grade' => $query->grade,
						'department_id' => $query->department_id,
						'is_active' => $query->is_active,
					);
					$after = array(
						'grade' => $request->has('grade') ? $request->grade : $query->grade,
						'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id,
						'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active,
					);
					break;
			}
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		}
	}
}
