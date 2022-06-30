<?php

namespace Modules\Academic\Repositories\Student;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\StudentMutation;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use CpChart\Data;
use CpChart\Image;
use Carbon\Carbon;

class StudentReportEloquent implements StudentReportRepository
{
    use HelperTrait;

	public function studentStatData(Request $request)
	{
        if ($request->has('data')) 
        {
            $payload = json_decode($request->data);
            $category = $payload->category;
        } else {
            $payload = $request;
            $category = $request->category;
        }
        switch ($category) 
        {
            case 'blood_type':
                $query = Students::select(
                                DB::raw('COUNT(academic.students.id) as y'),
                                DB::raw('UPPER("references".name) as subject'),
                                'references.id'
                            )
                            ->where('is_active', 1)
                            ->where('year_entry', $payload->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($payload) {
                                $qry->where('department_id', $payload->department_id);
                            })
                            ->join('references','references.id','=','academic.students.blood')
                            ->groupBy('references.name','references.id')
                            ->get();
                break;
            case 'gender':
                $query = Students::select(
                                DB::raw('COUNT(id) as y'),
                                DB::raw("CASE WHEN gender = 1 THEN 'Ikhwan' ELSE 'Akhwat' END as subject"),
                                DB::raw("gender as id")
                            )
                            ->where('is_active', 1)
                            ->where('year_entry', $payload->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($payload) {
                                $qry->where('department_id', $payload->department_id);
                            })
                            ->groupBy('gender')
                            ->get();
                break;
            case 'tribe':
                $query = Students::select(
                                DB::raw('COUNT(academic.students.id) as y'),
                                DB::raw('UPPER("references".name) as subject'),
                                'references.id'
                            )
                            ->where('is_active', 1)
                            ->where('year_entry', $payload->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($payload) {
                                $qry->where('department_id', $payload->department_id);
                            })
                            ->join('references','references.id','=','academic.students.tribe')
                            ->groupBy('references.name','references.id')
                            ->get();
                break;
            case 'born':
                $query = Students::select(
                                DB::raw('COUNT(id) as y'),
                                DB::raw("EXTRACT(YEAR FROM dob) as subject"),
                                DB::raw("EXTRACT(year FROM dob) as id")
                            )
                            ->where('is_active', 1)
                            ->where('year_entry', $payload->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($payload) {
                                $qry->where('department_id', $payload->department_id);
                            })
                            ->groupByRaw('EXTRACT(YEAR FROM dob)')
                            ->get();
                break;
            case 'age':
                $query = Students::select(
                                DB::raw("COUNT(id) as y"),
                                DB::raw("EXTRACT(year FROM age(NOW(),dob)) as subject"),
                                DB::raw("EXTRACT(year FROM age(NOW(),dob)) as id")
                            )
                            ->where('is_active', 1)
                            ->where('year_entry', $payload->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($payload) {
                                $qry->where('department_id', $payload->department_id);
                            })
                            ->groupByRaw('EXTRACT(year FROM age(NOW(),dob))')
                            ->get();
                break;
            default:
                $query = Students::select(
                                DB::raw('COUNT(academic.students.id) as y'),
                                DB::raw('academic.school_origins.school as subject'),
                                'academic.school_origins.id'
                            )
                            ->where('is_active', 1)
                            ->where('year_entry', $payload->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($payload) {
                                $qry->where('department_id', $payload->department_id);
                            })
                            ->leftJoin('academic.school_origins','academic.school_origins.id','=','academic.students.school_origin_id')
                            ->groupBy('academic.school_origins.school','academic.school_origins.id')
                            ->get();
                break;
        }
        return $query;
	}

    public function studentStatDataDetail(Request $request)
    {
        switch ($request->category) 
        {
            case 'blood_type':
                $query = Students::where('is_active', 1)
                            ->where('year_entry', $request->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($request) {
                                $qry->where('department_id', $request->department_id);
                            })
                            ->where('blood', $request->id)
                            ->get();
                break;
            case 'gender':
                $query = Students::where('is_active', 1)
                            ->where('year_entry', $request->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($request) {
                                $qry->where('department_id', $request->department_id);
                            })
                            ->where('gender', $request->id)
                            ->get();
                break;
            case 'tribe':
                $query = Students::where('is_active', 1)
                            ->where('year_entry', $request->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($request) {
                                $qry->where('department_id', $request->department_id);
                            })
                            ->where('tribe', $request->id)
                            ->get();
                break;
            case 'born':
                $query = Students::where('is_active', 1)
                            ->where('year_entry', $request->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($request) {
                                $qry->where('department_id', $request->department_id);
                            })
                            ->whereRaw('EXTRACT(year FROM dob) = ?', [$request->id])
                            ->get();
                break;
            case 'age':
                $query = Students::where('is_active', 1)
                            ->where('year_entry', $request->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($request) {
                                $qry->where('department_id', $request->department_id);
                            })
                            ->whereRaw('EXTRACT(year FROM age(NOW(),dob)) = ?', [$request->id])
                            ->get();
                break;
            default:
                $query = Students::where('is_active', 1)
                            ->where('year_entry', $request->generation_id)
                            ->whereHas('getClass.getSchoolYear', function($qry) use ($request) {
                                $qry->where('department_id', $request->department_id);
                            })
                            ->where('school_origin_id', $request->id)
                            ->get();
                break;
        }
        return $query;
    }

    public function studentMutationStatData($start, $end, $department_id)
    {
        $query = Reference::select('id',DB::raw('INITCAP(name) as mutation'))
                    ->selectRaw('? as start, ? as end, ? as department_id', [$start, $end, $department_id]);
        if ($department_id > 1)
        {
            $query =  $query->selectRaw("(
                                SELECT COUNT(b.id) FROM academic.student_mutations b
                                WHERE b.mutation_id = \"references\".id AND DATE_PART('year', b.mutation_date) >= ? AND DATE_PART('year', b.mutation_date) <= ? AND department_id = ?) as total"
                            ,[$start, $end, $department_id]);
        } else {
            $query =  $query->selectRaw("(
                                SELECT COUNT(b.id) FROM academic.student_mutations b
                                WHERE b.mutation_id = \"references\".id AND DATE_PART('year', b.mutation_date) >= ? AND DATE_PART('year', b.mutation_date) <= ?) as total"
                            ,[$start, $end]);
        }
        return $query = $query->where('category', 'hr_student_mutation')
                            ->orderBy('id', 'asc')
                            ->get();
    }

    public function studentMutationStatDataDetail($start, $end, $department_id, $mutation_id)
    {
        $query = StudentMutation::whereYear('mutation_date','>=',$start)->whereYear('mutation_date','<=',$end);
        if ($department_id > 1)
        {
            $query = $query->where('department_id', $department_id);
        }
        if ($mutation_id > 0)
        {
            $query = $query->where('mutation_id', $mutation_id);
        }
        return $query->orderBy('id','asc')->get()->map(function ($model) {
            $model['id_mutation'] = $model->mutation_id; 
            $model['student_no'] = $model->getStudent->student_no; 
            $model['student_id'] = $model->getStudent->name; 
            $model['mutation_date'] = $this->formatDate($model->mutation_date,'local'); 
            $model['department'] = $model->getDepartment->name;
            return $model;
        });
    }

    public function studentMutationGraph($start, $end, $department_id, $department)
    {
        $query = $this->studentMutationStatData($start, $end, $department_id);
        //
        $data_y = array();
        $label_y = array();
        if (count($query) > 0) 
        {
            foreach ($query as $row) 
            {
                if ($row->total > 0)
                {
                    $label_y[] = $this->formatLabel($row->mutation);
                    $data_y[] = $row->total;
                }
            }
            $data = new Data();
            $data->addPoints($data_y, "Jumlah");
            $data->setAxisName(0, "Jumlah");
            $data->addPoints($label_y, "Status");
            $data->setSerieDescription("Status", "Status");
            $data->setAbscissa("Status");

            /* Create the Image object */
            $image = new Image(380, 340, $data);
            $image->setFontProperties(["FontName" => "verdana.ttf", "FontSize" => 7]);

            /* Draw the chart scale */
            $image->setGraphArea(30, 25, 360, 300);
            $image->drawScale([
                "Mode"=> SCALE_MODE_ADDALL_START0,
            ]);

            /* Draw the chart */
            $image->drawBarChart(["DisplayValues" => true]);
            return '<img width="380px" height="340px" src="data:image/png;base64,'.base64_encode($image).'"/>';
        } 
    }
}