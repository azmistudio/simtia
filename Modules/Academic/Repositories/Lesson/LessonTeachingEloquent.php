<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\LessonSchedule;
use Modules\Academic\Entities\LessonScheduleTime;
use Modules\Academic\Entities\LessonScheduleTeaching;
use Carbon\Carbon;

class LessonTeachingEloquent implements LessonTeachingRepository
{
    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

    public function create(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return LessonScheduleTeaching::create($payload);
    }

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['schoolyear_id','grade_id','dg','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return LessonScheduleTeaching::where('id', $payload['id'])->update($payload);
    }

    public function data(Request $request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = LessonScheduleTeaching::select('*');
        // filter
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '')
        {
            $query = $query->where('department_id', $fdept);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '')
        {
            $query = $query->whereHas('getEmployee', function($qry) {
                $qry->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
            });
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department_id'] = $model->getDepartment->name;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['class_id'] = $model->getClass->class;
            return $model->only(['id','department_id','employee_id','employee','class_id']);
        });
        return $result;
    }

    public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return LessonScheduleTeaching::destroy($id);
    }

    public function dataRecap(Request $request)
    {
        // request
        $page = isset($request->page) ? intval($request->page) : 1;
        $rows = isset($request->rows) ? intval($request->rows) : 10;
        $sort = isset($request->sort) ? 'academic.lesson_schedules.' . $request->sort : 'academic.lesson_schedules.employee_id';
        $order = isset($request->order) ? $request->order : 'asc';
        $code = isset($request->params['fschedule']) ? $request->params['fschedule'] : 0;
        // query
        $query = LessonSchedule::select(
            'academic.lesson_schedules.employee_id',
            'academic.lesson_schedules.class_id',
            DB::raw('SUM(CASE WHEN academic.lesson_schedules.teaching_status = 37 THEN academic.lesson_schedules.teaching_status ELSE 0 END) AS teaching'),
            DB::raw('SUM(CASE WHEN academic.lesson_schedules.teaching_status = 38 THEN academic.lesson_schedules.teaching_status ELSE 0 END) AS assist'),
            DB::raw('SUM(CASE WHEN academic.lesson_schedules.teaching_status = 39 THEN academic.lesson_schedules.teaching_status ELSE 0 END) AS addition'),
            DB::raw('SUM(academic.lesson_schedules.to_time) AS time'),
            DB::raw('COUNT(DISTINCT(academic.lesson_schedules.day)) AS day'),
        )
        ->join('employees','employees.id','=','academic.lesson_schedules.employee_id')
        ->where('academic.lesson_schedules.schedule_id', $code)
        ->groupBy('academic.lesson_schedules.employee_id','academic.lesson_schedules.class_id','employees.employee_id','employees.name');
        // result
        $result["total"] = $query->distinct()->count('employees.employee_id');
        $result["rows"] = $query->skip(($page - 1) * $rows)->take($rows)->orderBy($sort, $order)->get()->map(function($model){
            $model['employee_no'] = $model->getEmployee->employee_id;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['class'] = $model->getClasses->class;
            return $model;
        });
        return $result;
    }

    public function comboGrid(Request $request)
    {
        $param = $this->gridRequest($request,'asc','lesson_id');
        $query = DB::table('academic.lesson_schedules_view')->select(
                        'academic.lesson_schedules_view.seq',
                        'academic.lesson_schedules_view.lesson',
                        'academic.lesson_schedules_view.teaching_status',
                        'academic.lesson_schedules_view.status',
                        'academic.lesson_schedules_view.employee_id',
                        DB::raw('academic.lesson_schedules_view.deptname as department'),
                        'academic.lesson_schedules_view.grade',
                        'academic.lesson_schedules_view.school_year',
                        'academic.lesson_schedules_view.semester',
                        'academic.lesson_schedules_view.class',
                        'academic.lesson_schedules_view.period',
                        'academic.lesson_schedules_view.id_class',
                        'academic.lesson_schedules_view.lesson_id',
                        'academic.lesson_schedules_view.semester_id',
                        'academic.lesson_schedules_view.department_id',
                        'academic.lesson_schedules_view.grade_id',
                        DB::raw("CONCAT(employees.title_first,' ',INITCAP(employees.name), ' ', employees.title_end) AS employee_name"),
                    )
                    ->join('employees','employees.id','=','academic.lesson_schedules_view.employee_id');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        // filter
        $class = isset($request->fclass) ? $request->fclass : '';
        if ($class != '') 
        {
            $query = $query->where('academic.lesson_schedules_view.id_class', $class);
        }
        $dept = isset($request->fdept) ? $request->fdept : '';
        if ($dept != '') 
        {
            $query = $query->where('academic.lesson_schedules_view.department_id', $dept);
        }
        $semester = isset($request->fsemester) ? $request->fsemester : '';
        if ($semester != '') 
        {
            $query = $query->where('academic.lesson_schedules_view.semester_id', $semester);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    public function comboBox($seq)
    {
        $params = explode('-', $seq);
        $query = LessonSchedule::where('class_id', $params[0])
                    ->where('employee_id', $params[1])
                    ->where('department_id', $params[2])
                    ->where('lesson_id', $params[3])
                    ->get();
        foreach ($query as $val) 
        {
            $result[] = array(
                'id' => $val->id,
                'time' => 'Hari '. $val->day .', Jam ke ' . $val->getTimeId1->time .' - '. $val->getTimeId2->time,
                'duration' => $val->to_time,
                'desc' => $this->getTime($val->time_id_1, $params[2]) . ' s.d ' . $this->getTime($val->time_id_2, $params[2])
            );
        }
        return $result;
    }

    private function getTime($time, $department_id)
    {
        $query = LessonScheduleTime::select('start','end')->where('id', $time)->where('department_id', $department_id)->first();
        return substr($query->start, 0, 5) .' - '. substr($query->end, 0, 5);
    }

    public function comboBoxDay($request)
    {
        $query = LessonSchedule::where('id', $request->seq)->first();
        $startdate = SchoolYear::where('is_active', 1)->first();
        $days = DB::select("SELECT * FROM academic.fn_get_presence_lessons(
                    ".$query->day.",
                    '".$startdate->start_date."',
                    ".$query->class_id.",
                    ".$query->lesson_id.",
                    ".$query->employee_id."
                )");
        if (count($days) > 0)
        {
            foreach ($days as $val) 
            {
                $result[] = array(
                    'id' => $val->date_gen,
                    'text' => $val->day .', '. $this->formatDate($val->date_gen,'local'),
                );
            }
        } else {
            $result = array();
        }
        return $result;
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'class_id' => $request->class_id, 
                'department_id' => $request->department_id, 
                'employee_id' => $request->employee_id, 
                'schedule_id' => $request->schedule_id, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = LessonScheduleTeaching::find($model_id);
            $before = array(
                'class_id' => $query->class_id, 
                'department_id' => $query->department_id, 
                'employee_id' => $query->employee_id, 
                'schedule_id' => $query->schedule_id, 
            );
            $after = array(
                'class_id' => $request->has('class_id') ? $request->class_id : $query->class_id, 
                'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
                'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
                'schedule_id' => $request->has('schedule_id') ? $request->schedule_id : $query->schedule_id, 
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