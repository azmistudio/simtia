<?php

namespace Modules\Academic\Repositories\Admission;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Academic\Entities\AdmissionProspectGroup;
use Modules\Academic\Entities\AdmissionProspect;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\HelperTrait;
use App\Models\Reference;


class AdmissionReportEloquent implements AdmissionReportRepository
{

    use ReferenceTrait;
    use HelperTrait;

	public function admissionStatData(Request $request)
	{
        if ($request->has('data')) 
        {
            $payload = json_decode($request->data);
            $category = $payload->category;
        } else {
            $payload = $request;
            $category = $request->category;
        }
        $admission_group = AdmissionProspectGroup::where('admission_id', $payload->admission_id)->first();
        switch ($category) 
        {
            case 'blood_type':
                $query = AdmissionProspect::select(
                                DB::raw('COUNT(academic.prospect_students.id) as y'),
                                DB::raw('UPPER("references".name) as label'),
                                'references.id'
                            )
                            ->where('prospect_group_id', $admission_group->id)
                            ->join('references','references.id','=','academic.prospect_students.blood')
                            ->groupBy('references.name','references.id')
                            ->get();
                break;
            case 'gender':
                $query = AdmissionProspect::select(
                                DB::raw('COUNT(id) as y'),
                                DB::raw("CASE WHEN gender = 1 THEN 'Ikhwan' ELSE 'Akhwat' END as label"),
                                DB::raw("gender as id")
                            )
                            ->where('prospect_group_id', $admission_group->id)
                            ->groupBy('gender')
                            ->get();
                break;
            case 'tribe':
                $query = AdmissionProspect::select(
                                DB::raw('COUNT(academic.prospect_students.id) as y'),
                                DB::raw('UPPER("references".name) as label'),
                                'references.id'
                            )
                            ->where('prospect_group_id', $admission_group->id)
                            ->join('references','references.id','=','academic.prospect_students.tribe')
                            ->groupBy('references.name','references.id')
                            ->get();
                break;
            case 'born':
                $query = AdmissionProspect::select(
                                DB::raw('COUNT(id) as y'),
                                DB::raw("EXTRACT(YEAR FROM dob) as label"),
                                DB::raw("EXTRACT(year FROM dob) as id")
                            )
                            ->where('prospect_group_id', $admission_group->id)
                            ->groupByRaw('EXTRACT(YEAR FROM dob)')
                            ->get();
                break;
            case 'age':
                $query = AdmissionProspect::select(
                                DB::raw("COUNT(id) as y"),
                                DB::raw("EXTRACT(year FROM age(NOW(),dob)) as label"),
                                DB::raw("EXTRACT(year FROM age(NOW(),dob)) as id")
                            )
                            ->where('prospect_group_id', $admission_group->id)
                            ->groupByRaw('EXTRACT(year FROM age(NOW(),dob))')
                            ->get();
                break;
            default:
                $query = AdmissionProspect::select(
                                DB::raw('COUNT(academic.prospect_students.id) as y'),
                                DB::raw('academic.school_origins.school as label'),
                                'academic.school_origins.id'
                            )
                            ->where('prospect_group_id', $admission_group->id)
                            ->groupBy('academic.school_origins.school','academic.school_origins.id')
                            ->get();
                break;
        }
        return $query;
	}

    public function admissionStatDataDetail(Request $request)
    {
        $admission_group = AdmissionProspectGroup::where('admission_id', $request->admission_id)->first();
        switch ($request->category) 
        {
            case 'blood_type':
                $query = AdmissionProspect::select(
                                'academic.prospect_students.registration_no',
                                'academic.prospect_students.name',
                                DB::raw("UPPER(academic.prospect_student_groups.group) as groups"),
                            )
                            ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                            ->where('prospect_group_id', $admission_group->id)
                            ->where('blood', $request->id)
                            ->get();
                break;
            case 'gender':
                $query = AdmissionProspect::select(
                                'academic.prospect_students.registration_no',
                                'academic.prospect_students.name',
                                DB::raw("UPPER(academic.prospect_student_groups.group) as groups"),
                            )
                            ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                            ->where('prospect_group_id', $admission_group->id)
                            ->where('gender', $request->id)
                            ->get();
                break;
            case 'tribe':
                $query = AdmissionProspect::select(
                                'academic.prospect_students.registration_no',
                                'academic.prospect_students.name',
                                DB::raw("UPPER(academic.prospect_student_groups.group) as groups"),
                            )
                            ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                            ->where('prospect_group_id', $admission_group->id)
                            ->where('tribe', $request->id)
                            ->get();
                break;
            case 'born':
                $query = AdmissionProspect::select(
                                'academic.prospect_students.registration_no',
                                'academic.prospect_students.name',
                                DB::raw("UPPER(academic.prospect_student_groups.group) as groups"),
                            )
                            ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                            ->where('prospect_group_id', $admission_group->id)
                            ->whereRaw('EXTRACT(year FROM dob) = ?', [$request->id])
                            ->get();
                break;
            case 'age':
                $query = AdmissionProspect::select(
                                'academic.prospect_students.registration_no',
                                'academic.prospect_students.name',
                                DB::raw("UPPER(academic.prospect_student_groups.group) as groups"),
                            )
                            ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                            ->where('prospect_group_id', $admission_group->id)
                            ->whereRaw('EXTRACT(year FROM age(NOW(),dob)) = ?', [$request->id])
                            ->get();
                break;
            default:
                $query = AdmissionProspect::select(
                                'academic.prospect_students.registration_no',
                                'academic.prospect_students.name',
                                DB::raw("UPPER(academic.prospect_student_groups.group) as groups"),
                            )
                            ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','academic.prospect_students.prospect_group_id')
                            ->where('prospect_group_id', $admission_group->id)
                            ->where('school_origin_id', $request->id)
                            ->get();
                break;
        }
        return $query;
    }

    public function admissionProspectData(Request $request)
    {
        $param = $this->gridRequest($request);
        $query = AdmissionProspect::select('*')->with('getStudent', function($qry){
            $qry->where('alumni',0);
        });
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getProspectGroup.getAdmission', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // filter
        $fgroup = isset($request->prospect_group_id) ? $request->prospect_group_id : '';
        if ($fgroup != '') 
        {
            $query = $query->where('prospect_group_id', $fgroup);
        }
        $fname = isset($request->student) ? $request->student : '';
        if ($fname != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($fname).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getProspectGroup->getAdmission->getDepartment->name;
            $model['admission'] = $model->getProspectGroup->getAdmission->name;
            $model['prospect_group'] = $model->getProspectGroup->group;
            $model['registration_no'] = Str::upper($model->registration_no);
            $model['gender'] = $this->getGender()[$model->gender];
            $model['class'] = $model->student_id == 0 || is_null($model->getStudent) ? '-' : $model->getStudent->getClass->getGrade->grade .'/'. $model->getStudent->getClass->class;
            $model['status'] = $model->is_active == 1 ? 'Aktif' : 'Tidak Aktif';
            return $model;
        });
        return $result;
    }

}