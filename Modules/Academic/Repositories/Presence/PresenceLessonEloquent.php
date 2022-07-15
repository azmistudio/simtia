<?php

namespace Modules\Academic\Repositories\Presence;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\PresenceLesson;
use Modules\Academic\Entities\PresenceLessonStudent;
use CpChart\Data;
use CpChart\Image;
use Carbon\Carbon;

class PresenceLessonEloquent implements PresenceLessonRepository
{

	use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return PresenceLesson::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), [
            'start','end','hour','minute','teach_hour', 'student_no', 'presence',
            'name', 'teach_minute','students','created_at','_token'
        ]);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return PresenceLesson::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request,'desc','academic.presence_lessons.id');
        $query = PresenceLesson::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSemester', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // filter
        $fdept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($fdept != '') 
        {
            $query = $query->whereHas('getSemester', function($qry) use ($fdept) {
                $qry->where('department_id', $fdept);
            });
        }
        $class = isset($request->params['fclass']) ? $request->params['fclass'] : '';
        if ($class != '') 
        {
            $query = $query->where('class_id', $class);
        }
        $lesson = isset($request->params['flesson']) ? $request->params['flesson'] : '';
        if ($lesson != '') 
        {
            $query = $query->where('lesson_id', $lesson);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) use ($request) {
            $model['id'] = $model->id;
            $model['department_id'] = $model->getSemester->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getSemester->semester;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['teacher'] = $model->getEmployee->name;
            $model['status'] = $model->getTeacherType->name;
            $model['date'] = $this->formatDate($model->date, 'local');
            $model['class_id'] = $model->getClass->class;
            $model['start_date'] = $model->getClass->getSchoolYear->start_date;
            $model['end_date'] = $model->getClass->getSchoolYear->end_date;
            $model['lesson'] = $model->getLesson->name;
            $model['lesson_schedule_id'] = 'Jam ke ' . $model->getLessonSchedule->getTimeId1->time .' - '. $model->getLessonSchedule->getTimeId2->time;
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return PresenceLesson::destroy($id);
	}

	public function list($request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = PresenceLessonStudent::where('presence_id', $request->id);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) use ($request) {
            $model['id'] = $model->getStudent->id;
            $model['student_no'] = $model->getStudent->student_no;
            $model['name'] = $model->getStudent->name;
            $model['presence'] = $this->getPresence()[$model->presence];
            return $model->only(['id','student_no','name','presence','remark']);
        });
        return $result;
    }

    public function combogrid($request)
    {
        $query = PresenceLesson::select(
                        'academic.presence_lessons.employee_id',
                        DB::raw('UPPER(departments.name) as department'),
                        'academic.classes.schoolyear_id',
                        DB::raw('UPPER(academic.schoolyears.school_year) as school_year'),
                        DB::raw('INITCAP("references".name) as t_type'),
                        DB::raw('employees.employee_id as id_employee'),
                        'academic.schoolyears.department_id'
                    )
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
                    ->join('employees','employees.id','=','academic.presence_lessons.employee_id')
                    ->join('departments','departments.id','=','academic.schoolyears.department_id')
                    ->join('references','references.id','=','academic.presence_lessons.teacher_type')
                    ->where('employees.is_active', 1)
                    ->groupBy(
                        'academic.presence_lessons.employee_id',
                        'departments.name',
                        'academic.classes.schoolyear_id',
                        'academic.schoolyears.school_year',
                        'employees.employee_id',
                        'employees.name',
                        'references.name',
                        'academic.schoolyears.department_id'
                    );
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getClass.getSchoolYear', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        } 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->where('employees.name', 'like', '%'.$filter.'%');
        }
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('academic.presence_lessons.employee_id')->get()->map(function($model){
            $model['seq'] = $model->employee_id.$model->department_id.$model->schoolyear_id;
            $model['employee'] = $model->id_employee .' - '. $this->getEmployeeName($model->employee_id);
            return $model;
        });
        return $result;
    }

    public function reportData($request)
    {
        $param = $this->gridRequest($request,'asc','academic.presence_lessons.date');
        $query = $this->queryReportData($request->start_date, $request->end_date, $request->student_id, 0);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['date'] = $this->formatDate($model->date,'local');
            return $model;
        });
        return $result;
    }

    public function queryReportData($start_date, $end_date, $student_id, $is_presence)
    {
        $query = PresenceLesson::select(
                        DB::raw('academic.presence_lessons.date as date'),
                        'academic.presence_lessons.time',
                        DB::raw('UPPER(academic.classes.class) as class'),
                        'academic.presence_lessons.remark',
                        DB::raw('UPPER(academic.lessons.name) as lesson'),
                        DB::raw('INITCAP(employees.name) as employee'),
                        'academic.presence_lessons.subject',
                    )
                    ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->join('academic.semesters','academic.semesters.id','=','academic.presence_lessons.semester_id')
                    ->join('academic.lessons','academic.lessons.id','=','academic.presence_lessons.lesson_id')
                    ->join('employees','employees.id','=','academic.presence_lessons.employee_id')
                    ->where('academic.presence_lesson_students.student_id', $student_id)
                    ->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                        $this->formatDate($start_date,'sys'), 
                        $this->formatDate($end_date,'sys')
                    ]);
        if ($is_presence > 0)
        {
           $query = $query->where('academic.presence_lesson_students.presence','<>',0);
        } else {
            $query = $query->where('academic.presence_lesson_students.presence',0);
        }
        return $query;
    }

    public function reportDataInfo($request)
    {
        $sum_present = $this->queryReportData($request->start_date, $request->end_date, $request->student_id, 0)->count();
        $sum_absent = $this->queryReportData($request->start_date, $request->end_date, $request->student_id, 1)->count();
        $total = $sum_present + $sum_absent;
        $total_val = $total > 0 ? $total : 1;
        $percent = $total == 0 ? 0 : round((($sum_present/$total_val)*100), 2);
        return array(
            'present' => $sum_present,
            'absent' => $sum_absent,
            'total' => $total,
            'percent' => $percent,
        );
    }

    public function reportPresenceLessonClass($request)
    {
        $param = $this->gridRequest($request,'asc','student_no');
        $query = $this->queryReportDataClass($request->class_id, $request->semester_id, $request->lesson_id, $request->start_date, $request->end_date);
        // result
        $result["total"] = $query->distinct()->count('student_no');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy('academic.students.'.$param['sort'], $param['sort_by'])->get()->map(function($model){
            $parent_guardian = $model->parent_guardian != '' ? ' / ' . $model->parent_guardian : '';
            $model['is_active'] = $model->is_active;
            $model['student_no'] = $model->student_no;
            $model['student'] = $model->student;
            $model['sum_present'] = $model->sum_present;
            $model['sum_absent'] = $model->sum_absent;
            $model['sum_total'] = $model->sum_present + $model->sum_absent;
            $model['sum_percent'] = ($model->sum_present + $model->sum_absent) == 0 ? '0%' : round(($model->sum_present/($model->sum_present + $model->sum_absent))*100, 2);
            $model['mobile'] = $model->mobile;
            $model['parent'] = $model->father.' / '.$model->mother . $parent_guardian;
            $model['parent_mobile'] = ($model->father_mobile != '' ? $model->father_mobile : '08...').' / '.($model->mother_mobile != '' ? $model->mother_mobile : '08...');
            return $model;
        });
        return $result;
    }

    public function queryReportDataClass($class_id, $semester_id, $lesson_id, $start_date, $end_date)
    {
        if ($lesson_id > 0)
        {
            $query = PresenceLessonStudent::selectRaw('
                        academic.students.student_no,
                        academic.presence_lesson_students.student_id, 
                        INITCAP(academic.students.name) as student,
                        academic.students.mobile, 
                        academic.students.father,
                        academic.students.mother,
                        academic.students.parent_guardian,
                        academic.students.father_mobile,
                        academic.students.mother_mobile,
                        academic.students.is_active,
                        (
                            SELECT COUNT(a.id) FROM academic.presence_lesson_students a
                            JOIN academic.presence_lessons b ON b.id = a.presence_id
                            WHERE a.student_id = academic.presence_lesson_students.student_id
                            AND a.presence = 0 AND b.class_id = ? AND b.semester_id = ? AND b.date BETWEEN ? AND ? AND b.lesson_id = ?
                        ) AS sum_present,
                        (
                            SELECT COUNT(a.id) FROM academic.presence_lesson_students a
                                JOIN academic.presence_lessons b ON b.id = a.presence_id
                                WHERE a.student_id = academic.presence_lesson_students.student_id
                                AND a.presence <> 0 AND b.class_id = ? AND b.semester_id = ? AND b.date BETWEEN ? AND ? AND b.lesson_id = ?
                            
                        ) AS sum_absent
                    ',[
                        $class_id, 
                        $semester_id, 
                        $this->formatDate($start_date,'sys'), 
                        $this->formatDate($end_date,'sys'),
                        $lesson_id,
                        $class_id, 
                        $semester_id, 
                        $this->formatDate($start_date,'sys'), 
                        $this->formatDate($end_date,'sys'),
                        $lesson_id
                    ])
                    ->where('academic.students.is_active', 1);
        } else {
            $query = PresenceLessonStudent::selectRaw('
                        academic.students.student_no,
                        academic.presence_lesson_students.student_id, 
                        INITCAP(academic.students.name) as student,
                        academic.students.mobile, 
                        academic.students.father,
                        academic.students.mother,
                        academic.students.parent_guardian,
                        academic.students.father_mobile,
                        academic.students.mother_mobile,
                        academic.students.is_active,
                        (
                            SELECT COUNT(a.id) FROM academic.presence_lesson_students a
                            JOIN academic.presence_lessons b ON b.id = a.presence_id
                            WHERE a.student_id = academic.presence_lesson_students.student_id
                            AND a.presence = 0 AND b.class_id = ? AND b.semester_id = ? AND b.date BETWEEN ? AND ?
                        ) AS sum_present,
                        (
                            SELECT COUNT(a.id) FROM academic.presence_lesson_students a
                                JOIN academic.presence_lessons b ON b.id = a.presence_id
                                WHERE a.student_id = academic.presence_lesson_students.student_id
                                AND a.presence <> 0 AND b.class_id = ? AND b.semester_id = ? AND b.date BETWEEN ? AND ?
                            
                        ) AS sum_absent
                    ',[
                        $class_id, 
                        $semester_id, 
                        $this->formatDate($start_date,'sys'), 
                        $this->formatDate($end_date,'sys'),
                        $class_id, 
                        $semester_id, 
                        $this->formatDate($start_date,'sys'), 
                        $this->formatDate($end_date,'sys')
                    ])
                    ->where('academic.students.is_active', 1);
        }
        //
        $query = $query->join('academic.presence_lessons','academic.presence_lessons.id','=','academic.presence_lesson_students.presence_id')
                       ->join('academic.students','academic.students.id','=','academic.presence_lesson_students.student_id')
                       ->where('academic.students.class_id', $class_id)
                       ->where('academic.presence_lessons.semester_id', $semester_id)
                       ->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                            $this->formatDate($start_date,'sys'), 
                            $this->formatDate($end_date,'sys')
                        ]);
        return $query;
    }

    public function reportPresenceLessonTeacher($request)
    {
        $param = $this->gridRequest($request,'asc','academic.presence_lessons.date');
        $query = PresenceLesson::selectRaw("
                        academic.presence_lessons.id, 
                        academic.presence_lessons.date, 
                        academic.presence_lessons.time, 
                        UPPER(academic.classes.class) as class,
                        INITCAP(academic.lessons.name) as lesson,
                        (
                            SELECT public.references.name FROM academic.teachers 
                            JOIN public.references ON public.references.id = status_id
                            WHERE employee_id = ? AND lesson_id = academic.presence_lessons.lesson_id
                        ) as status,
                        academic.presence_lessons.late,
                        to_char(academic.presence_lessons.times, 'HH:MI') as times,
                        academic.presence_lessons.subject,
                        academic.presence_lessons.remark
                    ", [$request->employee_id])
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->join('academic.lessons','academic.lessons.id','=','academic.presence_lessons.lesson_id')
                    ->where('academic.presence_lessons.employee_id', $request->employee_id)
                    ->where('academic.classes.schoolyear_id', $request->schoolyear_id)
                    ->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                        $this->formatDate($request->start_date,'sys'), 
                        $this->formatDate($request->end_date,'sys')
                    ]);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['id'] = $model->id;
            $model['date'] = $this->formatDate($model->date,'local');
            $model['time'] = $model->time;
            $model['class'] = $model->class;
            $model['lesson'] = $model->lesson;
            $model['status'] = $model->status;
            $model['late'] = $model->late;
            $model['times'] = $model->times;
            $model['subject'] = $model->subject;
            $model['remark'] = $model->remark;
            $model['minutes'] = $this->totalMinutes($model->times);
            return $model;
        });
        return $result;
    }

    public function reportPresenceLessonAbsent($request)
    {
        $param = $this->gridRequest($request,'asc','academic.presence_lessons.date');
        $query = PresenceLesson::select(
                        'academic.presence_lesson_students.id',
                        'academic.presence_lessons.date',
                        DB::raw('UPPER(academic.lessons.name) as lesson'),
                        'academic.presence_lesson_students.presence',
                        'academic.presence_lesson_students.remark',
                        'academic.presence_lesson_students.student_id',
                        'academic.students.student_no',
                        DB::raw('UPPER(academic.classes.class) as class'),
                        DB::raw('INITCAP(academic.students.name) as student'),
                        'academic.students.mobile',
                        DB::raw('INITCAP(academic.students.father) as father'),
                        DB::raw('INITCAP(academic.students.mother) as mother'),
                        DB::raw('INITCAP(academic.students.parent_guardian) as parent_guardian'),
                        'academic.students.father_mobile',
                        'academic.students.mother_mobile'
                    )
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->join('academic.lessons','academic.lessons.id','=','academic.presence_lessons.lesson_id')
                    ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                    ->join('academic.students','academic.students.id','=','academic.presence_lesson_students.student_id')
                    ->where('academic.presence_lessons.semester_id', $request->semester_id)
                    ->where('academic.presence_lesson_students.presence','<>',0)
                    ->where('academic.students.is_active', 1);
                    // filter
                    if ($request->grade_id > 0)
                    {
                        $query = $query->where('academic.classes.grade_id', $request->grade_id);
                    }
                    if ($request->class_id > 0)
                    {
                        $query = $query->where('academic.presence_lessons.class_id', $request->class_id);
                    }
                    $query->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                        $this->formatDate($request->start_date,'sys'), 
                        $this->formatDate($request->end_date,'sys')
                    ]);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $parent_guardian = $model->parent_guardian != '' ? ' / ' . $model->parent_guardian : '';
            $model['id'] = $model->id;
            $model['date'] = Carbon::createFromFormat('Y-m-d',$model->date)->format('d/m/Y');
            $model['lesson'] = $model->lesson;
            $model['presence'] = $this->getPresence()[$model->presence];
            $model['remark'] = $model->remark;
            $model['student_id'] = $model->student_id;
            $model['student_no'] = $model->student_no;
            $model['class'] = $model->class;
            $model['student'] = $model->student;
            $model['mobile'] = $model->mobile;
            $model['parent'] = $model->father.' / '.$model->mother . $parent_guardian;
            $model['parent_mobile'] = ($model->father_mobile != '' ? $model->father_mobile : '08...').' / '.($model->mother_mobile != '' ? $model->mother_mobile : '08...');
            return $model;
        });
        return $result;
    }

    public function reportPresenceLessonReflection($request)
    {
        $param = $this->gridRequest($request,'asc','academic.presence_lessons.date');
        $query = PresenceLesson::select(
                        'academic.presence_lessons.date',
                        'academic.presence_lessons.time',
                        DB::raw('UPPER(academic.classes.class) as class'),
                        DB::raw('UPPER("references".name) as status'),
                        DB::raw('UPPER(academic.lessons.name) as lesson'),
                        'academic.presence_lessons.subject',
                        'academic.presence_lessons.remark',
                        'academic.presence_lessons.objective',
                        'academic.presence_lessons.reflection',
                        'academic.presence_lessons.plan',
                    )
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->join('academic.lessons','academic.lessons.id','=','academic.presence_lessons.lesson_id')
                    ->join('employees','employees.id','=','academic.presence_lessons.employee_id')
                    ->join('academic.grades','academic.grades.id','=','academic.classes.grade_id')
                    ->join('academic.teachers','academic.teachers.employee_id','=','academic.presence_lessons.employee_id')
                    ->join('references','references.id','=','academic.teachers.status_id')
                    ->where('academic.presence_lessons.semester_id', $request->semester_id)
                    ->where('academic.presence_lessons.employee_id', $request->employee_id);
                    // filter
                    if ($request->grade_id > 0)
                    {
                        $query = $query->where('academic.classes.grade_id', $request->grade_id);
                    }
                    if ($request->class_id > 0)
                    {
                        $query = $query->where('academic.presence_lessons.class_id', $request->class_id);
                    }
                    if ($request->lesson_id > 0)
                    {
                        $query = $query->where('academic.presence_lessons.lesson_id', $request->lesson_id);
                    }
                    $query->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                        $this->formatDate($request->start_date,'sys'), 
                        $this->formatDate($request->end_date,'sys')
                    ]);
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['date'] = $this->formatDate($model->date,'local');
            $model['time'] = $model->time;
            $model['class'] = $model->class;
            $model['status'] = $model->status;
            $model['lesson'] = $model->lesson;
            $model['subject'] = $model->subject;
            $model['plan'] = $model->plan;
            $model['remark'] = $model->remark;
            $model['reflection'] = '
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="40%" style="border-right:none;">Materi</td>
                            <td style="border-right:none;">:</td>
                            <td>&nbsp;'.$model->subject.'</td>
                        </tr>
                        <tr>
                            <td style="border-right:none;">Rencana</td>
                            <td style="border-right:none;">:</td>
                            <td>&nbsp;'.$model->plan.'</td>
                        </tr>
                        <tr>
                            <td style="border-right:none;">Ket. Kehadiran</td>
                            <td style="border-right:none;">:</td>
                            <td>&nbsp;'.$model->remark.'</td>
                        </tr>
                    </tbody>
                </table>
            ';
            return $model;
        });
        return $result;
    }

    public function reportPresenceLessonStat(Request $request)
    {
        $param = $this->gridRequest($request,'asc','academic.students.id');
        $query = $this->queryPresenceStat($request->department_id, $request->semester_id, $request->schoolyear_id, $request->grade_id, $request->class_id, $request->lesson_id, $request->start_date, $request->end_date)
                    ->groupBy('academic.students.id','academic.students.student_no','academic.students.name','academic.presence_lesson_students.presence');
        // result
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) use($request){
            $model['student_id'] = $model->id;
            $model['student_no'] = $model->student_no;
            $model['student'] = $model->student;
            $model['graph'] = $this->generateGraphStat($request->department_id, $request->semester_id, $request->schoolyear_id, $request->grade_id, $request->class_id, $request->lesson_id, $request->start_date, $request->end_date, $model->id);
            return $model;
        });
        $result["total"] = count($result['rows']);
        return $result;
    }

    private function queryPresenceStat($department_id, $semester_id, $schoolyear_id, $grade_id, $class_id, $lesson_id, $start_date, $end_date)
    {
        return PresenceLesson::select(
                    'academic.students.id',
                    'academic.students.student_no',
                    DB::raw('INITCAP(academic.students.name) AS student'),
                    DB::raw('CASE WHEN academic.presence_lesson_students.presence = 0 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS present'),
                    DB::raw('CASE WHEN academic.presence_lesson_students.presence = 1 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS permit'),
                    DB::raw('CASE WHEN academic.presence_lesson_students.presence = 2 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS sick'),
                    DB::raw('CASE WHEN academic.presence_lesson_students.presence = 3 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS absent'),
                    DB::raw('CASE WHEN academic.presence_lesson_students.presence = 4 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS leave'),
                )
                ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                ->join('academic.students','academic.students.id','=','academic.presence_lesson_students.student_id')
                ->where('academic.presence_lessons.semester_id', $semester_id)
                ->where('academic.presence_lessons.class_id', $class_id)
                ->where('academic.presence_lessons.lesson_id', $lesson_id)
                ->where('academic.students.is_active', 1)
                ->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                    $this->formatDate($start_date,'sys'), 
                    $this->formatDate($end_date,'sys')
                ]);
    }

    private function generateGraphStat($department_id, $semester_id, $schoolyear_id, $grade_id, $class_id, $lesson_id, $start_date, $end_date, $student_id)
    {
        $query = $this->queryPresenceStat($department_id, $semester_id, $schoolyear_id, $grade_id, $class_id, $lesson_id, $start_date, $end_date)
                    ->where('academic.presence_lesson_students.student_id', $student_id)
                    ->groupBy('academic.students.id','academic.students.student_no','academic.students.name','academic.presence_lesson_students.presence')
                    ->first();
        //
        $data = new Data();
        $data->addPoints([$query->present, $query->permit, $query->sick, $query->absent, $query->leave], "Jumlah");
        $data->setAxisName(0, "Jumlah");
        $data->addPoints(['Hadir','Ijin','Sakit','Alpa','Cuti'], "Status");
        $data->setSerieDescription("Status", "Status");
        $data->setAbscissa("Status");

        /* Create the Image object */
        $image = new Image(650, 100, $data);
        $image->setFontProperties(["FontName" => "verdana.ttf", "FontSize" => 7]);

        /* Draw the chart scale */
        $image->setGraphArea(50, 25, 480, 90);
        $image->drawScale([
            "Mode"=> SCALE_MODE_ADDALL_START0,
            "Pos" => SCALE_POS_TOPBOTTOM
        ]);

        /* Draw the chart */
        $image->drawBarChart(["DisplayValues" => true]);
        return '<img width="650px" height="100px" src="data:image/png;base64,'.base64_encode($image).'"/>';        
    }

    public function reportPresenceLessonStatClass(Request $request)
    {
        $param = $this->gridRequest($request,'asc','class_id');
        $query = PresenceLesson::select(
                        DB::raw('DISTINCT ON (academic.presence_lessons.class_id) academic.presence_lessons.class_id'),
                        DB::raw('UPPER(academic.classes.class) as class'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 0 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS present'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 1 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS permit'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 2 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS sick'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 3 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS absent'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 4 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS leave'),
                        DB::raw('COUNT(academic.students.id) AS students'),
                        DB::raw('COUNT(academic.presence_lessons.id) AS lessons'),
                    )
                    ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                    ->join('academic.students','academic.students.id','=','academic.presence_lesson_students.student_id')
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->where('academic.presence_lessons.semester_id', $request->semester_id)
                    ->where('academic.presence_lessons.lesson_id', $request->lesson_id)
                    ->where('academic.students.is_active', 1)
                    ->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                        $this->formatDate($request->start_date,'sys'), 
                        $this->formatDate($request->end_date,'sys')
                    ])
                    ->groupBy('academic.presence_lessons.class_id','academic.classes.class','academic.presence_lesson_students.presence');

        // result
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) use($request){
            $model['class'] = $model->class;
            $model['graph'] = $this->generateGraphStatClass($request->semester_id, $request->lesson_id, $request->start_date, $request->end_date, $model->class_id);
            return $model;
        });
        $result["total"] = count($result['rows']);
        return $result;
    }

    private function generateGraphStatClass($semester_id, $lesson_id, $start_date, $end_date, $class_id)
    {
        // query
        $query = PresenceLesson::select(
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 0 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS present'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 1 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS permit'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 2 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS sick'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 3 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS absent'),
                        DB::raw('CASE WHEN academic.presence_lesson_students.presence = 4 THEN COUNT(academic.presence_lesson_students.presence) ELSE 0 END AS leave'),
                        DB::raw('COUNT(academic.students.id) AS students'),
                        DB::raw('COUNT(academic.presence_lessons.id) AS lessons'),
                    )
                    ->join('academic.presence_lesson_students','academic.presence_lesson_students.presence_id','=','academic.presence_lessons.id')
                    ->join('academic.students','academic.students.id','=','academic.presence_lesson_students.student_id')
                    ->join('academic.classes','academic.classes.id','=','academic.presence_lessons.class_id')
                    ->where('academic.presence_lessons.semester_id', $semester_id)
                    ->where('academic.presence_lessons.lesson_id', $lesson_id)
                    ->where('academic.presence_lessons.class_id', $class_id)
                    ->where('academic.students.is_active', 1)
                    ->whereRaw('academic.presence_lessons.date BETWEEN ? AND ?', [
                        $this->formatDate($start_date,'sys'), 
                        $this->formatDate($end_date,'sys')
                    ])
                    ->groupBy('academic.presence_lesson_students.presence')
                    ->first();

        $total = $query->lessons * $query->students;
        $total_val = $total > 0 ? $total : 1;
        // graph
        $data = new Data();
        $data->addPoints([
            number_format(($query->present/$total_val) * 100, 2), 
            number_format(($query->permit/$total_val) * 100, 2), 
            number_format(($query->sick/$total_val) * 100, 2), 
            number_format(($query->absent/$total_val) * 100, 2), 
            number_format(($query->leave/$total_val) * 100, 2)
        ], "Jumlah (%)");
        $data->setAxisName(0, "Jumlah (%)");
        $data->addPoints(['Hadir','Ijin','Sakit','Alpa','Cuti'], "Status");
        $data->setSerieDescription("Status", "Status");
        $data->setAbscissa("Status");

        /* Create the Image object */
        $image = new Image(750, 100, $data);
        $image->setFontProperties(["FontName" => "verdana.ttf", "FontSize" => 7]);

        /* Draw the chart scale */
        $image->setGraphArea(50, 25, 480, 90);
        $image->drawScale([
            "Mode"=> SCALE_MODE_ADDALL_START0,
            "Pos" => SCALE_POS_TOPBOTTOM
        ]);

        /* Draw the chart */
        $image->drawBarChart(["DisplayValues" => true]);
        return '<img width="750px" height="100px" src="data:image/png;base64,'.base64_encode($image).'"/>';
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'class_id' => $request->class_id, 
				'semester_id' => $request->semester_id, 
				'lesson_id' => $request->lesson_id, 
				'date' => $request->date, 
				'time' => $request->time, 
                'employee_id' => $request->employee_id, 
                'subject' => $request->subject, 
                'teacher_type' => $request->teacher_type, 
                'lesson_schedule_id' => $request->lesson_schedule_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = PresenceLesson::find($model_id);
			$before = array(
				'class_id' => $query->class_id, 
				'semester_id' => $query->semester_id, 
				'lesson_id' => $query->lesson_id, 
				'date' => $query->date, 
				'time' => $query->time, 
                'employee_id' => $query->employee_id, 
                'subject' => $query->subject, 
                'teacher_type' => $query->teacher_type, 
                'lesson_schedule_id' => $query->lesson_schedule_id, 
			);
			$after = array(
				'class_id' => $request->has('class_id') ? $request->class_id : $query->class_id, 
				'semester_id' => $request->has('semester_id') ? $request->semester_id : $query->semester_id, 
				'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id, 
				'date' => $request->has('date') ? $request->date : $query->date, 
				'time' => $request->has('time') ? $request->time : $query->time, 
                'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
                'subject' => $request->has('subject') ? $request->subject : $query->subject, 
                'teacher_type' => $request->has('teacher_type') ? $request->teacher_type : $query->teacher_type, 
                'lesson_schedule_id' => $request->has('lesson_schedule_id') ? $request->lesson_schedule_id : $query->lesson_schedule_id, 
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