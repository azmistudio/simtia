<?php

namespace Modules\Academic\Repositories\Exam;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Exam;
use Modules\Academic\Entities\LessonPlan;
use Carbon\Carbon;

class ExamEloquent implements ExamRepository
{

	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Exam::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['assessment_id','student_no','name','score','remark','start','end','students','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        //
        return Exam::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = Exam::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSemester', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        }
        // filter
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
        $code = isset($request->params['fcode']) ? $request->params['fcode'] : '';
        if ($code != '')
        {
            $query = $query->where('code', 'like', '%'.Str::lower($code).'%');
        }
        // result
        $result["total"] = $query->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['lesson'] = $model->getLesson->name;
            $model['date'] = Carbon::createFromFormat('Y-m-d', $model->date)->format('d/m/Y');
            $model['code'] = Str::upper($model->code);
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['class_id'] = $model->getClass->class;
            return $model->only(['id','code','date','lesson','department','class_id']);
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return Exam::destroy($id);
	}

	public function comboGrid(Request $request)
    {
        $param = $this->gridRequest($request,'asc','id');
        $query = Exam::select('*');
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['lessonexam_id'] = $model->lesson_exam_id;
            $model['grade_id'] = $model->getClass->getGrade->id;
            $model['department'] = $model->getLesson->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['semester'] = $model->getSemester->semester;
            $model['class'] = $model->getClass->class;
            $model['lesson'] = $model->getLesson->lesson;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['remark'] = $model->getScoreAspect->remark;
            $model['subject'] = $model->getLessonExam->subject;
            return $model;
        });
        return $result;
    }

    public function comboGridExam(Request $request)
    {
        $param = $this->gridRequest($request,'asc','academic.exams.score_aspect_id');
        $query = Exam::select(
                        'academic.exams.lesson_id',
                        'academic.exams.class_id',
                        'academic.exams.semester_id',
                        'academic.exams.employee_id',
                        'academic.exams.score_aspect_id',
                        'academic.grades.id',
                        'departments.name',
                        'academic.grades.grade',
                        'academic.schoolyears.school_year',
                        'academic.semesters.semester',
                        'academic.classes.class',
                        'academic.lessons.name',
                        'employees.name',
                        'academic.score_aspects.remark',
                    )
                    ->join('employees','employees.id','=','academic.exams.employee_id')
    		        ->join('academic.semesters','academic.semesters.id','=','academic.exams.semester_id')
    		        ->join('academic.score_aspects','academic.score_aspects.id','=','academic.exams.score_aspect_id')
    		        ->join('academic.classes','academic.classes.id','=','academic.exams.class_id')
    		        ->join('academic.grades','academic.grades.id','=','academic.classes.grade_id')
    		        ->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
    		        ->join('academic.lessons','academic.lessons.id','=','academic.exams.lesson_id')
    		        ->join('departments','departments.id','=','academic.lessons.department_id')
                    ->where('academic.semesters.is_active',1)
    		        ->groupBy(
    		            'academic.exams.lesson_id',
    		            'academic.exams.class_id',
    		            'academic.exams.semester_id',
    		            'academic.exams.employee_id',
    		            'academic.exams.score_aspect_id',
    		            'academic.grades.id',
    		            'departments.name',
    		            'academic.grades.grade',
    		            'academic.schoolyears.school_year',
    		            'academic.semesters.semester',
    		            'academic.classes.class',
    		            'academic.lessons.name',
    		            'employees.name',
    		            'academic.score_aspects.remark',
    		        );
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->whereHas('getSemester', function($qry) {
                $qry->where('department_id', auth()->user()->department_id);
            });
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['seq'] = $model->lesson_id.$model->class_id.$model->semester_id.$model->employee_id.$model->getClass->grade_id.$model->score_aspect_id;
            $model['employee'] = $model->getEmployee->title_first .' '. $model->getEmployee->name .' '. $model->getEmployee->title_end;
            $model['grade_id'] = $model->getClass->grade_id;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['department'] = $model->getLesson->getDepartment->name;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['semester'] = $model->getSemester->semester;
            $model['class'] = $model->getClass->class;
            $model['lesson'] = $model->getLesson->name;
            $model['department_id'] = $model->getLesson->department_id;
            $model['remark'] = Str::title($model->remark);
            return $model;
        });
        return $result;
    }

    public function dataScore(Request $request)
    {
        // rows
        $query_rows = DB::select("SELECT academic.fn_pivotcode_exams('academic.exams_view','student_id','code','score',".$request->assessment_id.'::int8'.",".$request->class_id.'::int8'.",".$request->semester_id.'::int8'.",'numeric')");
        $rows = DB::select($query_rows[0]->fn_pivotcode_exams);
        // total
        $total = collect(DB::select($query_rows[0]->fn_pivotcode_exams));
        // footer
        $query_footer = DB::select("SELECT academic.fn_pivotcode_exams_avg('academic.exams_view','student_id','code','score',".$request->assessment_id.'::int8'.",".$request->class_id.'::int8'.",".$request->semester_id.'::int8'.",'numeric')");
        $footer = DB::select($query_footer[0]->fn_pivotcode_exams_avg);
        $result["total"] = $total->count();
        $result["rows"] = $rows;
        $result["footer"] = $footer;
        return $result;
    }

    public function dataScoreWeight(Request $request)
    {
        $query = Exam::select(
                    'academic.exams.id',
                    DB::raw("UPPER(academic.lesson_exams.code) as assessment"),
                    DB::raw("academic.exams.date"),
                    'academic.exams.lesson_plan_id',
                    DB::raw('academic.exam_score_final_weights.id as weight_id'),
                    'academic.exam_score_final_weights.score',
                    DB::raw('academic.lesson_exams.id as lessonexam')
                )
                ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exams.lesson_exam_id')
                ->leftJoin('academic.exam_score_final_weights','academic.exam_score_final_weights.exam_id','=','academic.exams.id')
                ->where('academic.exams.lesson_assessment_id', $request->assessment_id)
                ->where('academic.exams.class_id', $request->class_id)
                ->where('academic.exams.semester_id', $request->semester_id)
                ->orderBy('academic.exams.id')
                ->get();
        $result = array();
        $i = 1;
        foreach ($query as $row)
        {
            $result[] = array(
                'id' => $row->id,
                'weight_id' => $row->weight_id,
                'assessment' => $row->assessment . '-' . $i . ' (' . Carbon::createFromFormat('Y-m-d', $row->date)->format('d/m/Y') . ')' . '<br/>RPP: ' . $this->getLessonPlan($row->lesson_plan_id),
                'score' => $row->score,
                'lessonexam_id' => $row->lessonexam
            );
            $i++;
        }
        return $result;
    }

    public function reportAssessmentScores($lesson_id, $student_id, $class_id, $semester_id, $lesson_exam_id, $assessment_id)
    {
        $query = Exam::select(
                        'academic.exams.id',
                        'academic.exams.date',
                        'academic.exams.description',
                        'academic.exam_scores.score',
                        'academic.exams.lesson_exam_id',
                        'academic.exams.score_aspect_id',
                        'academic.exams.semester_id',
                    )
                    ->join('academic.exam_scores','academic.exam_scores.exam_id','=','academic.exams.id')
                    ->where('academic.exams.lesson_id', $lesson_id)
                    ->where('academic.exam_scores.student_id', $student_id)
                    ->where('academic.exams.class_id', $class_id)
                    ->where('academic.exams.semester_id', $semester_id)
                    ->where('academic.exams.lesson_exam_id', $lesson_exam_id)
                    ->where('academic.exams.lesson_assessment_id', $assessment_id)
                    ->get();

        $result = array();
        $num_score = 0;
        $avg_score = 0;
        foreach ($query as $row)
        {
            $avg_class = $this->getAvgClass($class_id, $row->id, $semester_id)[0];
            $num_score += $row->score;
            $result[] = array(
                'date' => Carbon::createFromFormat('Y-m-d',$row->date)->format('d/m/Y'),
                'description' => $row->description,
                'score' => number_format($row->score,2),
                'avg_class' => number_format($avg_class, 2),
                'percent' => round((($row->score - $avg_class) / $avg_class) * 100, 2) . '%',
                'avg_score' => count($query) > 0 ? number_format($num_score / count($query), 2) : 0,
                'final_score' => number_format($this->getFinalScore($class_id, $semester_id, $lesson_exam_id, $student_id, $lesson_id, $assessment_id)[0], 2)
            );
        }
        return $result;
    }

    private function getAvgClass($class_id, $exam_id, $semester_id)
    {
        return DB::table('academic.avg_score_classes')
                ->where('class_id', $class_id)
                ->where('exam_id', $exam_id)
                ->where('semester_id', $semester_id)
                ->pluck('avg_score');
    }

    private function getFinalScore($class_id, $semester_id, $lesson_exam_id, $student_id, $lesson_id, $assessment_id)
    {
        return DB::table('academic.avg_score_students')
                ->where('class_id', $class_id)
                ->where('semester_id', $semester_id)
                ->where('lesson_exam_id', $lesson_exam_id)
                ->where('student_id', $student_id)
                ->where('lesson_id', $lesson_id)
                ->where('lesson_assessment_id', $assessment_id)
                ->pluck('avg_score');
    }

    public function reportExams($lesson_id, $class_id, $semester_id)
    {
        return Exam::select(
                    'academic.exams.lesson_exam_id',
                    'academic.lesson_exams.code',
                    'academic.lesson_exams.subject',
                )
                ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exams.lesson_exam_id')
                ->where('academic.exams.lesson_id',$lesson_id)
                ->where('academic.exams.class_id',$class_id)
                ->where('academic.exams.semester_id',$semester_id)
                ->groupBy('academic.exams.lesson_exam_id','academic.lesson_exams.code','academic.lesson_exams.subject')
                ->get();
    }

    public function reportExamDates($lesson_id, $class_id, $semester_id)
    {
       return Exam::select(
                    'academic.exams.id',
                    'academic.exams.date',
                    'academic.exams.lesson_exam_id',
                )
                ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exams.lesson_exam_id')
                ->where('academic.exams.lesson_id',$lesson_id)
                ->where('academic.exams.class_id',$class_id)
                ->where('academic.exams.semester_id',$semester_id)
                ->orderBy('academic.exams.id', 'asc')
                ->get();
    }

    public function reportExamCount($lesson_id, $class_id, $semester_id)
    {
       return Exam::select(
                    'lesson_exam_id',
                    DB::raw('COUNT(id) as total')
                )
                ->where('lesson_id',$lesson_id)
                ->where('class_id',$class_id)
                ->where('semester_id',$semester_id)
                ->groupBy('lesson_exam_id')
                ->orderBy('lesson_exam_id','asc')
                ->get();
    }

    // helpers

    function getLessonPlan($id)
    {
        if ($id > 0)
        {
            return LessonPlan::select('code')->where('id', $id)->first()->code;
        } else {
            return 'Tanpa RPP';
        }
    }

	private function logActivity(Request $request, $model_id, $subject, $action)
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'teacher_id' => $request->teacher_id,
				'lesson_id' => $request->lesson_id,
				'class_id' => $request->class_id,
				'semester_id' => $request->semester_id,
				'employee_id' => $request->employee_id,
				'status_id' => $request->status_id,
				'score_aspect_id' => $request->score_aspect_id,
				'lesson_exam_id' => $request->lesson_exam_id,
				'date' => $request->date,
				'lesson_assessment_id' => $request->lesson_assessment_id,
				'lesson_plan_id' => $request->lesson_plan_id,
				'code' => $request->code,
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Exam::find($model_id);
			$before = array(
				'teacher_id' => $query->teacher_id,
				'lesson_id' => $query->lesson_id,
				'class_id' => $query->class_id,
				'semester_id' => $query->semester_id,
				'employee_id' => $query->employee_id,
				'status_id' => $query->status_id,
				'score_aspect_id' => $query->score_aspect_id,
				'lesson_exam_id' => $query->lesson_exam_id,
				'date' => $query->date,
				'lesson_assessment_id' => $query->lesson_assessment_id,
				'lesson_plan_id' => $query->lesson_plan_id,
				'code' => $query->code,
			);
			$after = array(
				'teacher_id' => $request->has('teacher_id') ? $request->teacher_id : $query->teacher_id,
				'lesson_id' => $request->has('lesson_id') ? $request->lesson_id : $query->lesson_id,
				'class_id' => $request->has('class_id') ? $request->class_id : $query->class_id,
				'semester_id' => $request->has('semester_id') ? $request->semester_id : $query->semester_id,
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id,
				'status_id' => $request->has('status_id') ? $request->status_id : $query->status_id,
				'score_aspect_id' => $request->has('score_aspect_id') ? $request->score_aspect_id : $query->score_aspect_id,
				'lesson_exam_id' => $request->has('lesson_exam_id') ? $request->lesson_exam_id : $query->lesson_exam_id,
				'date' => $request->has('date') ? $request->date : $query->date,
				'lesson_assessment_id' => $request->has('lesson_assessment_id') ? $request->lesson_assessment_id : $query->lesson_assessment_id,
				'lesson_plan_id' => $request->has('lesson_plan_id') ? $request->lesson_plan_id : $query->lesson_plan_id,
				'code' => $request->has('code') ? $request->code : $query->code,
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
