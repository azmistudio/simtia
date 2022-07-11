<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\File;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Entities\LessonPlan;
use CpChart\Data;
use CpChart\Image;
use Carbon\Carbon;

class LessonPlanEloquent implements LessonPlanRepository
{
    use AuditLogTrait;
    use HelperTrait;
    use ReferenceTrait;
    
	public function create(Request $request, $subject)
	{
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return LessonPlan::create($payload);
	}

	public function update(Request $request, $subject)
	{
        $request->offsetUnset('lesson_plan_file.*');
        $payload = $request->only(['id','deptid','grade_id','semester_id','lesson_id','code','subject','description','is_active','logged']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return LessonPlan::where('id', $payload['id'])->update($payload);
	}

    public function showIn($params)
    {
        return LessonPlan::whereIn('id', $params)->orderBy('id')->get()->map(function ($model) {
            $model['is_active'] = $this->getActive()[$model->is_active];
            return $model;
        });
    }

	public function data(Request $request)
	{
        $param = $this->gridRequest($request,'asc','id');
        $query = LessonPlan::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
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
            $query = $query->whereRaw('LOWER(subject) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) {
            $model['department_id'] = $model->getDepartment->name;
            $model['grade_id'] = $model->getGrade->grade;
            $model['semester_id'] = $model->getSemester->semester;
            $model['lesson'] = $model->getLesson->name;
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return LessonPlan::destroy($id);
	}

    public function combobox(Request $request)
    {
        $query = LessonPlan::where('department_id', $request->department_id)
                    ->where('grade_id', $request->grade_id)
                    ->where('semester_id', $request->semester_id)
                    ->where('lesson_id', $request->lesson_id)
                    ->where('is_active', 1)
                    ->orderBy('id')
                    ->get();

        $result[] = array('id' => '', 'text' => '---');
        array_push($result, array('id' => 0, 'text' => 'Tanpa RPP'));
        foreach ($query as $row) 
        {
            $result[] = array(
                'id' => $row->id,
                'text' => Str::title($row->subject),
            );
        }
        return $result;
    }

    public function planClassData(Request $request)
    {
        $query = LessonPlan::select(
                        'id',
                        'code',
                        'subject',
                        DB::raw('lesson_id as id_lesson'),
                    )
                    ->where('grade_id', $request->grade_id)
                    ->where('semester_id', $request->semester_id)
                    ->where('lesson_id', $request->lesson_id)
                    ->where('is_active', 1)
                    ->orderBy('code')
                    ->get();
        foreach ($query as $row) 
        {
            $result['rows'][] = array(
                'value' => $row->id .'-'. $row->id_lesson,
                'text' => Str::upper($row->code) .' | '. $row->subject,
            );
        }
        if (count($query) > 0)
        {
            return $result;
        } else {
            $response['rows'] = array();
            return $response;
        }
    }

    public function planClassGraph($semester_id, $lesson_exam_id, $lesson_plan_id, $lesson_id, $grade_id)
    {
        $query = DB::table('academic.exam_scores')->select(
                        DB::raw('UPPER(academic.classes.class) as class'),
                        DB::raw('SUM(academic.exam_scores.score) / (COUNT(DISTINCT academic.classes.id) * COUNT(academic.students.id)) as total')
                    )
                    ->join('academic.students','academic.students.id','=','academic.exam_scores.student_id')
                    ->join('academic.exams','academic.exams.id','=','academic.exam_scores.exam_id')
                    ->join('academic.classes','academic.classes.id','=','academic.exams.class_id')
                    ->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
                    ->join('academic.lesson_plans','academic.lesson_plans.id','=','academic.exams.lesson_plan_id')
                    ->join('academic.grades','academic.grades.id','=','academic.lesson_plans.grade_id')
                    ->where('academic.exams.semester_id', $semester_id)
                    ->where('academic.exams.lesson_exam_id', $lesson_exam_id)
                    ->where('academic.exams.lesson_plan_id', $lesson_plan_id)
                    ->where('academic.exams.lesson_id', $lesson_id)
                    ->where('academic.students.is_active', 1)
                    ->where('academic.grades.id', $grade_id)
                    ->where('academic.grades.is_active', 1)
                    ->where('academic.schoolyears.is_active', 1)
                    ->groupBy('academic.classes.id')
                    ->get();
        //
        $data_y = array();
        $label_y = array();
        $data = array();
        if (count($query) > 0) 
        {
            foreach ($query as $row) 
            {
                $label_y[] = $row->class;
                $data_y[] = number_format($row->total,2);
                $data[] = array(
                    'class' => $row->class,
                    'total' => number_format($row->total, 2),
                );
            }

            $dataGraph = new Data();
            $dataGraph->addPoints($data_y, "Jumlah");
            $dataGraph->setAxisName(0, "Jumlah");
            $dataGraph->addPoints($label_y, "Status");
            $dataGraph->setSerieDescription("Status", "Status");
            $dataGraph->setAbscissa("Status");

            /* Create the Image object */
            $image = new Image(450, 300, $dataGraph);
            $image->setFontProperties(["FontName" => "verdana.ttf", "FontSize" => 7]);

            /* Draw the chart scale */
            $image->setGraphArea(50, 25, 480, 280);
            $image->drawScale([
                "Mode"=> SCALE_MODE_ADDALL_START0,
            ]);

            /* Draw the chart */
            $image->drawBarChart(["DisplayValues" => true]);
            return array($data, '<img width="450px" height="300px" src="data:image/png;base64,'.base64_encode($image).'"/>');
        } else {
            return $data;
        }
    }

    public function planStudentGraph($semester_id, $class_id, $lesson_exam_id, $lesson_plan_id, $lesson_id, $grade_id)
    {
        $union = DB::table('academic.students')->select(
                        'academic.students.student_no',
                        DB::raw('0 as total'),
                        DB::raw('INITCAP(academic.students.name) as student')
                    )
                    ->where('class_id', $class_id)
                    ->where('is_active', 1)
                    ->whereRaw('
                        id NOT IN (
                            SELECT a.id FROM academic.students a, academic.exam_scores b, academic.exams c, academic.lesson_exams d
                            WHERE b.exam_id = c.id AND c.semester_id = ? AND c.class_id = ? AND c.lesson_exam_id = ? AND c.lesson_plan_id = ? AND c.lesson_id = ?
                            AND a.id = b.student_id AND c.lesson_exam_id = d.id AND a.class_id = ? AND a.is_active = 1 GROUP BY a.id
                        )
                    ', [$semester_id, $class_id, $lesson_exam_id, $lesson_plan_id, $lesson_id, $class_id]);
        //
        $query = DB::table('academic.exam_scores')->select(
                        'academic.students.student_no',
                        DB::raw('SUM(academic.exam_scores.score) / (COUNT(DISTINCT academic.exams.id)) as total'),
                        DB::raw('INITCAP(academic.students.name) as student')
                    )
                    ->join('academic.students','academic.students.id','=','academic.exam_scores.student_id')
                    ->join('academic.exams','academic.exams.id','=','academic.exam_scores.exam_id')
                    ->join('academic.classes','academic.classes.id','=','academic.exams.class_id')
                    ->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
                    ->join('academic.lesson_plans','academic.lesson_plans.id','=','academic.exams.lesson_plan_id')
                    ->join('academic.grades','academic.grades.id','=','academic.lesson_plans.grade_id')
                    ->where('academic.exams.semester_id', $semester_id)
                    ->where('academic.exams.class_id', $class_id)
                    ->where('academic.exams.lesson_exam_id', $lesson_exam_id)
                    ->where('academic.exams.lesson_plan_id', $lesson_plan_id)
                    ->where('academic.exams.lesson_id', $lesson_id)
                    ->where('academic.students.class_id', $class_id)
                    ->where('academic.students.is_active', 1)
                    ->groupBy('academic.students.id')
                    ->union($union)
                    ->get();
        //
        $data_y = array();
        $label_y = array();
        $data = array();
        if (count($query) > 0)
        {
            foreach ($query as $row) 
            {
                $label_y[] = $row->student_no;
                $data_y[] = number_format($row->total,2);
                $data[] = array(
                    'student_no' => $row->student_no,
                    'student' => $row->student,
                    'total' => number_format($row->total,2),
                );
            }

            $dataGraph = new Data();
            $dataGraph->addPoints($data_y, "Jumlah");
            $dataGraph->setAxisName(0, "Jumlah");
            $dataGraph->addPoints($label_y, "Status");
            $dataGraph->setSerieDescription("Status", "Status");
            $dataGraph->setAbscissa("Status");

            /* Create the Image object */
            $image = new Image(460, 300, $dataGraph);
            $image->setFontProperties(["FontName" => "verdana.ttf", "FontSize" => 7]);

            /* Draw the chart scale */
            $image->setGraphArea(80, 25, 450, 100);
            $image->drawScale([
                "Mode"=> SCALE_MODE_ADDALL_START0,
                "Pos" => SCALE_POS_TOPBOTTOM
            ]);

            /* Draw the chart */
            $image->drawBarChart(["DisplayValues" => true]);
            return array($data, '<img width="460px" height="300px" src="data:image/png;base64,'.base64_encode($image).'"/>');
        } else {
            return $data;
        }
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'grade_id' => $request->grade_id, 
                'department_id' => $request->department_id, 
                'semester_id' => $request->semester_id, 
                'lesson_id' => $request->lesson_id, 
                'code' => $request->code, 
                'subject' => $request->subject, 
                'is_active' => $request->is_active, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = LessonPlan::find($model_id);
            $before = array(
                'grade_id' => $query->grade_id, 
                'department_id' => $query->department_id, 
                'semester_id' => $query->semester_id, 
                'lesson_id' => $query->lesson_id, 
                'code' => $query->code, 
                'subject' => $query->subject, 
                'is_active' => $query->is_active, 
            );
            $after = array(
                'grade_id' => $request->has('grade_id') ? $request->grade_id : $query->grade_id, 
                'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
                'semester_id' => $request->has('semester_id') ? $request->semester_id : $query->semester_id, 
                'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id, 
                'code' => $request->has('code') ? $request->code : $query->code, 
                'subject' => $request->has('subject') ? $request->subject : $query->subject, 
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