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
use Modules\Academic\Entities\PresenceDaily;
use Modules\Academic\Entities\Students;
use Carbon\Carbon;
use CpChart\Data;
use CpChart\Image;

class PresenceDailyEloquent implements PresenceDailyRepository
{

	use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return PresenceDaily::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['start','end','month','year','start_date','end_date','students','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return PresenceDaily::where('id', $payload['id'])->update($payload);
	}


	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = PresenceDaily::select('*');
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
        $semester = isset($request->params['fsemester']) ? $request->params['fsemester'] : '';
        if ($semester != '') 
        {
            $query = $query->where('semester_id', $semester);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['class_id'] = $model->getClass->class;
            $model['department_id'] = $model->getSemester->getDepartment->name;
            $model['period'] = $model->start_date->format('d/m/Y') .' s.d '. $model->end_date->format('d/m/Y');
            return $model->only(['id','class_id','department_id','semester_id','start_date','end_date','active_day','period']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return PresenceDaily::destroy($id);
	}

	public function list(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = Students::select('*');
        // filter
        $class = isset($request->fclass) ? $request->fclass : '';
        if ($class != '') 
        {
            $query = $query->where('class_id', $class);
        }
        if ($request->id != 0)
        {
            $query = $query->whereHas('getPresenceDailyStudent', function ($qry) use ($request) {
                $qry->where('presence_id', $request->id);
            });
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) use ($request) {
            $presenceDailyStudent = $model->getPresenceDailyStudent->where('student_id', $model->id)->where('presence_id', $request->id);
            $model['present'] = $presenceDailyStudent->pluck('present')->first();
            $model['permit'] = $presenceDailyStudent->pluck('permit')->first();
            $model['sick'] = $presenceDailyStudent->pluck('sick')->first();
            $model['absent'] = $presenceDailyStudent->pluck('absent')->first();
            $model['leave'] = $presenceDailyStudent->pluck('leave')->first();
            $model['remark'] = $presenceDailyStudent->pluck('remark')->first();
            return $model->only(['id','student_no','name','present','permit','sick','absent','leave','remark']);
        });
        return $result;
    }

    public function reportData($start_date, $end_date, $student_id)
    {
        $query = PresenceDaily::select(
                    DB::raw('UPPER(academic.classes.class) as class'),
                    DB::raw('UPPER(academic.semesters.semester) as semester'),
                    'academic.presence_dailies.start_date',
                    'academic.presence_dailies.end_date',
                    DB::raw('SUM(academic.presence_daily_students.present) as present'),
                    DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                    DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                    DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                    DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                )
                ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                ->join('academic.classes','academic.classes.id','=','academic.presence_dailies.class_id')
                ->join('academic.semesters','academic.semesters.id','=','academic.presence_dailies.semester_id')
                ->where('academic.presence_daily_students.student_id', $student_id)
                ->whereRaw('(
                    academic.presence_dailies.start_date BETWEEN ? AND ? OR academic.presence_dailies.end_date BETWEEN ? AND ? OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date
                )', 
                [
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys'),
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys'),
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys')
                ])
                ->groupBy('academic.classes.class','academic.semesters.semester','academic.presence_dailies.start_date','academic.presence_dailies.end_date')
                ->orderBy('academic.presence_dailies.start_date')
                ->get();

        $present = 0;
        $permit = 0;
        $sick = 0;
        $absent = 0;
        $leave = 0;
        $data = array();
        foreach ($query as $row) 
        {
            $data[] = array(
                'date' => $this->formatDate($row->start_date,'localtime') .' s.d '. $this->formatDate($row->end_date,'localtime'),
                'semester' => $row->semester,
                'class' => $row->class,
                'present' => $row->present,
                'permit' => $row->permit,
                'sick' => $row->sick,
                'absent' => $row->absent,
                'leave' => $row->leave,
            );

            $present += $row->present;
            $permit += $row->permit;
            $sick += $row->sick;
            $absent += $row->absent;
            $leave += $row->leave;
        }

        $result["total"] = count($data);
        $result["rows"] = $data;
        $result["footer"][] = array(
            'class' => 'Jumlah',
            'present' => $present,
            'permit' => $permit,
            'sick' => $sick,
            'absent' => $absent,
            'leave' => $leave,
        );
        return $result;
    }

    public function reportPresenceDailyClass(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'academic.classes.class');
        $query = PresenceDaily::select(
                        DB::raw('UPPER(academic.classes.class) as class'),
                        DB::raw('UPPER(academic.semesters.semester) as semester'),
                        DB::raw('SUM(academic.presence_daily_students.present) as present'),
                        DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                        DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                        DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                        DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                        'academic.students.student_no',
                        DB::raw('INITCAP(academic.students.name) AS student')
                    )
                    ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                    ->join('academic.classes','academic.classes.id','=','academic.presence_dailies.class_id')
                    ->join('academic.semesters','academic.semesters.id','=','academic.presence_dailies.semester_id')
                    ->join('academic.students','academic.students.id','=','academic.presence_daily_students.student_id')
                    ->where('academic.presence_dailies.class_id', $request->class_id)
                    ->where('academic.students.is_active', 1)
                    ->whereRaw('(
                        academic.presence_dailies.start_date BETWEEN ? AND ? OR academic.presence_dailies.end_date BETWEEN ? AND ? OR
                        ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date OR
                        ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date
                    )', 
                    [
                        $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys'),
                        $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys'),
                        $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys')
                    ])
                    ->groupBy('academic.classes.class','academic.semesters.semester','academic.students.student_no','academic.students.name');  
        //
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

    public function reportPresenceDailyAbsent(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'academic.students.id');
        // query
        $query = PresenceDaily::select(
                        'academic.students.id',
                        'academic.students.student_no',
                        DB::raw('INITCAP(academic.students.name) AS student'),
                        DB::raw('UPPER(academic.classes.class) as class'),
                        DB::raw('SUM(academic.presence_daily_students.present) as present'),
                        DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                        DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                        DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                        DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                    )
                    ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                    ->join('academic.classes','academic.classes.id','=','academic.presence_dailies.class_id')
                    ->join('academic.semesters','academic.semesters.id','=','academic.presence_dailies.semester_id')
                    ->join('academic.students','academic.students.id','=','academic.presence_daily_students.student_id')
                    ->where('academic.semesters.department_id', $request->department_id)
                    ->where('academic.classes.schoolyear_id', $request->schoolyear_id)
                    ->where('academic.presence_dailies.semester_id', $request->semester_id)
                    ->where('academic.students.is_active', 1);
                    // filter
                    if ($request->grade_id > 0)
                    {
                        $query = $query->where('academic.classes.grade_id', $request->grade_id);
                    }
                    if ($request->class_id > 0)
                    {
                        $query = $query->where('academic.presence_dailies.class_id', $request->class_id);
                    }
                    $query->whereRaw('(
                        academic.presence_dailies.start_date BETWEEN ? AND ? OR academic.presence_dailies.end_date BETWEEN ? AND ? OR
                        ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date OR
                        ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date
                    )', 
                    [
                        $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys'),
                        $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys'),
                        $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys')
                    ])->groupBy('academic.classes.class','academic.students.id','academic.students.student_no','academic.students.name')
                      ->havingRaw('SUM(academic.presence_daily_students.permit) > 0 OR SUM(academic.presence_daily_students.sick) > 0 OR SUM(academic.presence_daily_students.absent) > 0 OR SUM(academic.presence_daily_students.leave) > 0');
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['student_id'] = $model->id;
            $model['student_no'] = $model->student_no;
            $model['student'] = $model->student;
            $model['class'] = $model->class;
            $model['parent'] = $this->getStudentParent($model->id);
            $model['present'] = '<b>'.$model->present.'</b>';
            $model['permit'] = '<b>'.$model->permit.'</b>';
            $model['sick'] = '<b>'.$model->sick.'</b>';
            $model['absent'] = '<b>'.$model->absent.'</b>';
            $model['leave'] = '<b>'.$model->leave.'</b>';
            return $model;
        });
        return $result;
    }

    private function getStudentParent($student_id)
    {
        $student = Students::where('id', $student_id)->first();
        $info = 'No. HP: ' . $student->father_mobile . '/' . $student->mother_mobile .'<br/>';
        $info .= 'Email: ' . $student->father_email . '/' . $student->mother_email .'<br/>';
        $info .= 'Alamat: ' . $student->parent_address .'<br/>';
        $info .= 'Telpon: ' . $student->phone .'<br/>';
        $info .= 'HP Santri: ' . $student->mobile .'<br/>';
        $info .= 'Email Santri: ' . $student->email;
        return $info;
    }

    public function reportPresenceStat(Request $request)
    {
        $semesters = explode('-', $request->semester_id);
        $param = $this->gridRequest($request, 'asc', 'academic.students.id');
        $query = $this->queryPresenceStat($request->department_id, $request->semester_id, $request->schoolyear_id, $request->grade_id, $request->class_id, $request->start_date, $request->end_date)
                    ->groupBy('academic.classes.class','academic.students.id','academic.students.student_no','academic.students.name');
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) use($request){
            $model['student_id'] = $model->id;
            $model['student_no'] = $model->student_no;
            $model['student'] = $model->student;
            $model['graph'] = $this->generateGraphStat($request->department_id, $request->semester_id, $request->schoolyear_id, $request->grade_id, $request->class_id, $request->start_date, $request->end_date, $model->id);
            return $model;
        });
        return $result;
    }

    private function queryPresenceStat($department_id, $semester_id, $schoolyear_id, $grade_id, $class_id, $start_date, $end_date)
    {
        return PresenceDaily::select(
                    'academic.students.id',
                    'academic.students.student_no',
                    DB::raw('INITCAP(academic.students.name) AS student'),
                    DB::raw('UPPER(academic.classes.class) as class'),
                    DB::raw('SUM(academic.presence_daily_students.present) as present'),
                    DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                    DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                    DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                    DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                )
                ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                ->join('academic.classes','academic.classes.id','=','academic.presence_dailies.class_id')
                ->join('academic.semesters','academic.semesters.id','=','academic.presence_dailies.semester_id')
                ->join('academic.students','academic.students.id','=','academic.presence_daily_students.student_id')
                ->where('academic.semesters.department_id', $department_id)
                ->where('academic.classes.schoolyear_id', $schoolyear_id)
                ->where('academic.presence_dailies.semester_id', $semester_id)
                ->where('academic.classes.grade_id', $grade_id)
                ->where('academic.presence_dailies.class_id', $class_id)
                ->where('academic.students.is_active', 1)
                ->whereRaw('(
                    academic.presence_dailies.start_date BETWEEN ? AND ? OR academic.presence_dailies.end_date BETWEEN ? AND ? OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date
                )', 
                [
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys'),
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys'),
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys')
                ]);
    }

    private function generateGraphStat($department_id, $semester_id, $schoolyear_id, $grade_id, $class_id, $start_date, $end_date, $student_id)
    {
        $query = $this->queryPresenceStat($department_id, $semester_id, $schoolyear_id, $grade_id, $class_id, $start_date, $end_date)
                    ->where('academic.presence_daily_students.student_id', $student_id)
                    ->groupBy('academic.classes.class','academic.students.id','academic.students.student_no','academic.students.name')
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

    public function reportPresenceStatClass(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'academic.classes.id');
        // query
        $query = PresenceDaily::select(
                    DB::raw('academic.classes.id as id'),
                    DB::raw('UPPER(academic.classes.class) as class'),
                )
                ->join('academic.classes','academic.classes.id','=','academic.presence_dailies.class_id')
                ->join('academic.semesters','academic.semesters.id','=','academic.presence_dailies.semester_id')
                ->where('academic.semesters.department_id', $request->department_id)
                ->where('academic.classes.schoolyear_id', $request->schoolyear_id)
                ->where('academic.presence_dailies.semester_id', $request->semester_id)
                ->where('academic.classes.grade_id', $request->grade_id)
                ->whereRaw('(
                    academic.presence_dailies.start_date BETWEEN ? AND ? OR academic.presence_dailies.end_date BETWEEN ? AND ? OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date
                )', 
                [
                    $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys'),
                    $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys'),
                    $this->formatDate($request->start_date,'sys'), $this->formatDate($request->end_date,'sys')
                ])
                ->groupBy('academic.classes.class','academic.classes.id');

        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) use($request){
            $model['class'] = $model->class;
            $model['graph'] = $this->generateGraphStatClass($request->department_id, $request->semester_id, $request->schoolyear_id, $request->grade_id, $request->start_date, $request->end_date, $model->id);
            return $model;
        });
        return $result;
    }

    private function generateGraphStatClass($department_id, $semester_id, $schoolyear_id, $grade_id, $start_date, $end_date, $class_id)
    {
        $query = PresenceDaily::select(
                    DB::raw('SUM(academic.presence_daily_students.present) as present'),
                    DB::raw('SUM(academic.presence_daily_students.permit) as permit'),
                    DB::raw('SUM(academic.presence_daily_students.sick) as sick'),
                    DB::raw('SUM(academic.presence_daily_students.absent) as absent'),
                    DB::raw('SUM(academic.presence_daily_students.leave) as leave'),
                    DB::raw('COUNT(DISTINCT academic.students.student_no) as count_student'),
                )
                ->join('academic.presence_daily_students','academic.presence_daily_students.presence_id','=','academic.presence_dailies.id')
                ->join('academic.classes','academic.classes.id','=','academic.presence_dailies.class_id')
                ->join('academic.semesters','academic.semesters.id','=','academic.presence_dailies.semester_id')
                ->join('academic.students','academic.students.id','=','academic.presence_daily_students.student_id')
                ->where('academic.semesters.department_id', $department_id)
                ->where('academic.classes.schoolyear_id', $schoolyear_id)
                ->where('academic.presence_dailies.semester_id', $semester_id)
                ->where('academic.classes.grade_id', $grade_id)
                ->where('academic.presence_dailies.class_id', $class_id)
                ->where('academic.students.is_active', 1)
                ->whereRaw('(
                    academic.presence_dailies.start_date BETWEEN ? AND ? OR academic.presence_dailies.end_date BETWEEN ? AND ? OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date OR
                    ? BETWEEN academic.presence_dailies.start_date AND academic.presence_dailies.end_date
                )', 
                [
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys'),
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys'),
                    $this->formatDate($start_date,'sys'), $this->formatDate($end_date,'sys')
                ])->first();

        $total = $query->present + $query->permit + $query->sick + $query->absent + $query->leave;
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
				'start_date' => $request->start_date, 
				'end_date' => $request->end_date, 
				'active_day' => $request->active_day, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = PresenceDaily::find($model_id);
			$before = array(
				'class_id' => $query->class_id, 
				'semester_id' => $query->semester_id, 
				'start_date' => $query->start_date, 
				'end_date' => $query->end_date, 
				'active_day' => $query->active_day, 
			);
			$after = array(
				'class_id' => $request->has('class_id') ? $request->class_id : $query->class_id, 
				'semester_id' => $request->has('semester_id') ? $request->semester_id : $query->semester_id, 
				'start_date' => $request->has('start_date') ? $request->start_date : $query->start_date, 
				'end_date' => $request->has('end_date') ? $request->end_date : $query->end_date, 
				'active_day' => $request->has('active_day') ? $request->active_day : $query->active_day, 
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