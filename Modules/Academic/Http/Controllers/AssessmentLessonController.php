<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Entities\LessonGrading;
use Modules\Academic\Entities\LessonExam;
use Modules\Academic\Entities\LessonAssessment;
use Modules\Academic\Entities\LessonPlan;
use Modules\Academic\Entities\ScoreAspect;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\Exam;
use Modules\Academic\Entities\ExamScore;
use Modules\Academic\Entities\Teacher;
use Modules\HR\Entities\Employee;
use Modules\Academic\Http\Requests\ExamRequest;
use Modules\Academic\Repositories\Exam\ExamEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use View;
use Exception;

class AssessmentLessonController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Penilaian Pelajaran';
    private $subject_final = 'Data Nilai Akhir';

    function __construct(ExamEloquent $examEloquent)
    {
        $this->examEloquent = $examEloquent;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        //
        return view('academic::pages.assessments.assessment_lesson', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ExamRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $isValid = true;
            $dates = explode('/', $request->date);
            $assessment_id = explode('-', $request->assessment_id);
            $lessons = explode('-', $request->lesson_id);
            // check period
            if (
                strtotime($this->formatDate($request->date,'sys')) < strtotime($request->start) || 
                strtotime($this->formatDate($request->date,'sys')) > strtotime($request->end))
            {
                throw new Exception('Tanggal harus berada di rentang Periode.', 1);
            } else {
                for ($i=0; $i < count($request->students); $i++) 
                {
                    if ($request->students[$i]['score'] == '')
                    {
                        throw new Exception('Nilai Santri wajib diisi.', 1);
                    }
                }
            }
            // 
            $teachers = Teacher::select('id')->where('employee_id', $request->employee_id)->where('lesson_id', $lessons[3])->first();
            $request->merge([
                'date' => $this->formatDate($request->date, 'sys'),
                'teacher_id' => $teachers->id,
                'lesson_assessment_id' => $assessment_id[0],
                'code' => Str::lower($request->code),
                'lesson_id' => $lessons[3],
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $getFinalScoreByStudentId = DB::table('academic.exam_score_finals')->select('id','score','remark')->where('class_id',$request->class_id)->where('semester_id',$request->semester_id)->where('lesson_assessment_id',$assessment_id[0])->count();
                if ($getFinalScoreByStudentId > 0) 
                {
                    DB::table('academic.exam_score_finals')->where('class_id',$request->class_id)->where('semester_id',$request->semester_id)->where('lesson_assessment_id',$assessment_id[0])->delete();
                }
                $query = $this->examEloquent->create($request, $this->subject);
                for ($i=0; $i < count($request->students); $i++) 
                {
                    DB::table('academic.exam_scores')->insert([
                        'exam_id' => $query->id,
                        'student_id' => $request->students[$i]['id'],
                        'score' => $request->students[$i]['score'],
                        'remark' => $request->students[$i]['remark'],
                        'logged' => auth()->user()->email,
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);
                    $this->countAvgScoreStudent($request->class_id, $request->semester_id, $assessment_id[0], $request->students[$i]['id']);
                }
                $this->countAvgScoreClass($request->class_id, $request->semester_id, $assessment_id[0], $query->id);
                $response = $this->getResponse('store', '', $this->subject);
            } else {
                for ($i=0; $i < count($request->students); $i++) 
                {
                    DB::table('academic.exam_scores')->where('exam_id', $request->id)->where('student_id', $request->students[$i]['id'])->update([
                        'score' => $request->students[$i]['score'],
                        'remark' => $request->students[$i]['remark'],
                        'logged' => auth()->user()->email,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                    $this->countAvgScoreStudent($request->class_id, $request->semester_id, $assessment_id[0], $request->students[$i]['id']);
                }
                $this->countAvgScoreClass($request->class_id, $request->semester_id, $assessment_id[0], $request->id);
                $this->examEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Pelajaran dan Jenis Ujian');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return response()->json(Exam::where('id', $id)->get()->map(function($model){
            $model['department_id'] = $model->getSemester->department_id;
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['grade_id'] = $model->getClass->grade_id;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getSemester->semester;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['teacher'] = $model->getTeacher->getEmployee->name;
            $model['status'] = $model->getStatus->name;
            $model['start_date'] = $model->getClass->getSchoolYear->start_date;
            $model['end_date'] = $model->getClass->getSchoolYear->end_date;
            $model['lesson'] = $model->getLesson->name;
            $model['assessment'] = $model->getLessonExam->code;
            $model['class'] = $model->getClass->class;
            return $model;
        })[0]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        try 
        {
            $assessment_id = explode('-', $request->assessment_id);
            DB::table('academic.exam_scores')->where('exam_id', $id)->delete();
            DB::table('academic.avg_score_classes')->where('exam_id', $id)->delete();
            $this->examEloquent->destroy($id, $this->subject);
            for ($i=0; $i < count($request->students); $i++) 
            {
                $this->countAvgScoreStudent($request->class_id, $request->semester_id, $assessment_id[0], $request->students[$i]['id']);
            }
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->examEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function list($id)
    {
        $query = ExamScore::where('exam_id',$id)
                    ->get()->map(function ($model) {
                        $model['id'] = $model->student_id;
                        $model['student_no'] = $model->getStudent->student_no;
                        $model['name'] = $model->getStudent->name;
                        return $model;
                    });
        return response()->json($query);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->examEloquent->comboGrid($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGridExam(Request $request)
    {
        return response()->json($this->examEloquent->comboGridExam($request));
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexScore(Request $request, $id, $height)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $params = explode("-", $id);
        $data['exams'] = Exam::where('lesson_assessment_id', $params[0])->where('class_id', $params[1])->where('semester_id', $params[2])->get();
        $data['infos'] = Exam::where('id', $params[3])->get()->map(function($model){
            $model['department_id'] = $model->getSemester->department_id;
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getSemester->semester;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['teacher'] = $model->getTeacher->getEmployee->name;
            $model['status'] = $model->getStatus->name;
            $model['start_date'] = $model->getClass->getSchoolYear->start_date;
            $model['end_date'] = $model->getClass->getSchoolYear->end_date;
            $model['lesson'] = $model->getLesson->name;
            $model['assessment'] = $model->getLessonExam->code;
            $model['class'] = $model->getClass->class;
            $model['score_aspect'] = $model->getScoreAspect->remark;
            return $model;
        })[0];
        // 
        $data['params'] = $params;
        $data['InnerHeight'] = $height;
        return view('academic::pages.assessments.assessment_lesson_score', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataScore(Request $request)
    {
        return response()->json($this->examEloquent->dataScore($request));
    }

    /**
     * Display a listing of response.
     * @return JSON
     */
    public function scoreRecalc(Request $request)
    {
        $students = json_decode($request->students);
        foreach ($students as $key => $student) 
        {
            $this->countAvgScoreStudent($request->class_id, $request->semester_id, $request->assessment_id, $student->student_id);
        }
        //
        $exams = Exam::where('lesson_assessment_id', $request->assessment_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->get();
        foreach ($exams as $exam)
        {
            $this->countAvgScoreClass($request->class_id, $request->semester_id, $request->assessment_id, $exam->id);
        }
        $response = $this->getResponse('warning', 'Rata - Rata Kelas dan Santri sudah dihitung ulang.');
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataScoreWeight(Request $request)
    {
        return response()->json($this->examEloquent->dataScoreWeight($request));
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexScoreDialog(Request $request, $id)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $params = explode("-", $id);
        $data['exams'] = Exam::where('lesson_assessment_id', $params[0])->where('class_id', $params[1])->where('semester_id', $params[2])->get();
        $data['infos'] = Exam::where('id', $params[3])->get()->map(function($model){
            $model['department_id'] = $model->getSemester->department_id;
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['semester'] = $model->getSemester->semester;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['teacher'] = $model->getTeacher->getEmployee->name;
            $model['status'] = $model->getStatus->name;
            $model['start_date'] = $model->getClass->getSchoolYear->start_date;
            $model['end_date'] = $model->getClass->getSchoolYear->end_date;
            $model['lesson'] = $model->getLesson->name;
            $model['assessment'] = $model->getLessonExam->code;
            $model['class'] = $model->getClass->class;
            $model['score_aspect'] = $model->getScoreAspect->remark;
            return $model;
        })[0];
        // 
        $data['params'] = $params;
        return view('academic::pages.assessments.assessment_lesson_score_dialog', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function indexScoreDialogEdit(Request $request)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $exams = Exam::select('academic.exams.id','academic.lesson_exams.code')
                            ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exams.lesson_exam_id')
                            ->where('academic.exams.teacher_id', $request->teachers_id)
                            ->where('academic.exams.lesson_id', $request->lesson_id)
                            ->where('academic.exams.class_id', $request->class_id)
                            ->where('academic.exams.semester_id', $request->semester_id)
                            ->where('academic.exams.employee_id', $request->employee_id)
                            ->where('academic.exams.status_id', $request->status_id)
                            ->where('academic.exams.score_aspect_id', $request->score_aspect_id)
                            ->where('academic.exams.lesson_exam_id', $request->lesson_exam_id)
                            ->get();
        $ids = array();
        foreach ($exams as $exam) 
        {
            $ids[] = $exam->id;
        }
        $data['code'] = $exams[0]->code;
        $data['requests'] = $request->all();
        $data['scores'] = ExamScore::whereIn('exam_id', $ids)->where('student_id', $request->student_id)->get();
        return view('academic::pages.assessments.assessment_lesson_score_dialog_edit', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeFinal(Request $request)
    {
        $validated = $request->validate([
            'lesson_id' => 'required',
            'class_id' => 'required',
            'semester_id' => 'required',
            'lesson_exam_id' => 'required',
            'assessment_id' => 'required',
        ]);
        try 
        {
            if ($request->method <> 'manual')
            {
                $weights = json_decode($request->weights);
                for ($i=0; $i < count($weights); $i++) 
                {
                    if ($weights[$i]->score == '')
                    {
                        throw new Exception('Nilai Bobot item dicentang wajib diisi.', 1);
                    }
                }
                for ($i=0; $i < count($weights); $i++)
                {
                    if ($weights[$i]->weight_id == '')
                    {
                        DB::table('academic.exam_score_final_weights')->insert([
                            'exam_id' => $weights[$i]->id,
                            'lesson_exam_id' => $weights[$i]->lessonexam_id,
                            'score' => $weights[$i]->score,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        DB::table('academic.exam_score_final_weights')
                            ->where('id', $weights[$i]->weight_id)
                            ->update([
                                'score' => $weights[$i]->score,
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }
                //
                $query_student = Students::select('id','student_no')->where('class_id',$request->class_id)->where('is_active',1)->orderBy('name')->get();
                foreach ($query_student as $student) 
                {
                    $query_exam = Exam::select('id')->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->where('lesson_assessment_id', $request->assessment_id)->get();
                    $exam_index = 0;
                    $score = 0.0;
                    $weight = 0.0;
                    foreach ($query_exam as $exam) 
                    {
                        // get weight
                        $query_weight = DB::table('academic.exam_score_final_weights')->select('score')->where('exam_id', $exam->id)->first();
                        $w = isset($query_weight) ? floatval($query_weight->score) : 0.0;
                        // get exam score
                        $query_exam_score = ExamScore::select('score')->where('exam_id', $exam->id)->where('student_id', $student->id)->first();
                        $exam_score = floatval($query_exam_score->score);
                        // calc final score
                        $score = $score + $w * $exam_score;
                        $weight = $weight + $w;
                        $exam_index++;
                    }
                    $avground = $weight > 0 ? round(($score/$weight), 2) : round($score, 2);
                    DB::table('academic.exam_score_finals')->upsert([
                        [
                            'lesson_id' => $request->lesson_id,
                            'student_id' => $student->id,
                            'class_id' => $request->class_id,
                            'semester_id' => $request->semester_id,
                            'lesson_exam_id' => $request->lesson_exam_id,
                            'lesson_assessment_id' => $request->assessment_id,
                            'score' => $avground,
                            'remark' => 'Otomatis',
                            'logged' => auth()->user()->email,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]
                    ], ['lesson_id','student_id','class_id','semester_id','lesson_exam_id','lesson_assessment_id'], ['score', 'remark', 'updated_at']);
                }
                $response = $this->getResponse('store', '', $this->subject_final);
            } else {
                $students = json_decode($request->students);
                for ($i=0; $i < count($students); $i++) 
                {
                    if ($students[$i]->final_score == '')
                    {
                        throw new Exception('Nilai Akhir (NA) wajib diisi.', 1);
                    }
                }
                $query_student = Students::select('id','student_no')->where('class_id',$request->class_id)->where('is_active',1)->orderBy('name')->get();
                foreach ($query_student as $student) 
                {
                    for ($i=0; $i < count($students); $i++) 
                    {
                        if ($students[$i]->student_id == $student->id)
                        {
                            DB::table('academic.exam_score_finals')->upsert([
                                [
                                    'lesson_id' => $request->lesson_id,
                                    'student_id' => $students[$i]->student_id,
                                    'class_id' => $request->class_id,
                                    'semester_id' => $request->semester_id,
                                    'lesson_exam_id' => $request->lesson_exam_id,
                                    'lesson_assessment_id' => $request->assessment_id,
                                    'score' => floatval($students[$i]->final_score),
                                    'remark' => 'Manual',
                                    'logged' => auth()->user()->email,
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s'),
                                ]
                            ], ['lesson_id','student_id','class_id','semester_id','lesson_exam_id','lesson_assessment_id'], ['score', 'remark', 'updated_at']);
                        }
                    }
                }
                $response = $this->getResponse('store', '', $this->subject_final);
            }        
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject_final);
        }
        return response()->json($response);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeScoreEdit(Request $request)
    {
        $validated = $request->validate([
            'new_score.*' => 'required',
            'reason' => 'required',
        ]);
        $old_scores = array();
        for ($i=0; $i < count($request->score); $i++) 
        { 
            $values = explode('-', $request->score[$i]);
            $old_scores[] = $values[1];            
        }
        $diff = array_diff_assoc($old_scores, $request->new_score);
        if (count($diff) > 0)
        {
            try 
            {
                DB::transaction(function () use ($request, $diff) {
                    $i = 1;
                    foreach ($diff as $key => $val) 
                    {
                        $olds = explode('-', $request->score[$key]);
                        $infos = ExamScore::select(
                                        'academic.lesson_exams.code',
                                        'academic.lessons.name',
                                        'academic.exams.date',
                                        'academic.exams.lesson_id',
                                        'academic.exams.class_id',
                                        'academic.exams.semester_id',
                                        'academic.exams.lesson_exam_id',
                                        'academic.exams.lesson_assessment_id'
                                    )
                                    ->join('academic.exams','academic.exams.id','=','academic.exam_scores.exam_id')
                                    ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exams.lesson_exam_id')
                                    ->join('academic.lessons','academic.lessons.id','=','academic.exams.lesson_id')
                                    ->where('academic.exam_scores.id', $olds[0])
                                    ->first();
                        // audit log
                        DB::table('academic.audit_exam_scores')->insert([
                            'score_type' => 'examscore',
                            'exam_id' => $olds[0],
                            'score_before' => $olds[1],
                            'score_after' => $request->new_score[$key],
                            'date_trans' => date('Y-m-d H:i:s'),
                            'reason' => $request->reason,
                            'remark' => $request->remark,
                            'info' => 'Nilai Ujian ' . strtoupper($infos->code).'-'.$i. ' ' . strtoupper($infos->name) . ' tanggal ' . Carbon::createFromFormat('Y-m-d', $infos->date)->format('d/m/Y') . ' santri (' . $request->student_no . ') ' . $request->student_name,
                            'logged' => auth()->user()->email,
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);
                        // update score
                        ExamScore::where('id', $olds[0])->update([
                            'score' => $request->new_score[$key],
                            'logged' => auth()->user()->email,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                        // remove score final
                        DB::table('academic.exam_score_finals')
                            ->where('lesson_id', $infos->lesson_id)
                            ->where('student_id', $request->student_id)
                            ->where('class_id', $infos->class_id)
                            ->where('semester_id', $infos->semester_id)
                            ->where('lesson_exam_id', $infos->lesson_exam_id)
                            ->where('lesson_assessment_id', $infos->assessment_id)
                            ->delete();
                        // log
                        $i++;
                    }
                });
                $response = $this->getResponse('store', '', $this->subject);
            } catch (\Throwable $e) {
                $response = $this->getResponse('error', $e->getMessage(), $this->subject);
            }
        } else {
            $response = $this->getResponse('warning', 'Tidak ada nilai yang diubah.');
        }   
        return response()->json($response);
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcel(Request $request)
    {
        $query_exams = Exam::where('lesson_assessment_id', $request->assessment_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->get();
        $i = 1;
        //
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(strval(Str::of($this->subject)->snake()));
        $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
        $sheet->getPageSetup()->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4);
        //
        $sheet->getColumnDimension('A')->setWidth(6);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(30);
        //
        $sheet->setCellValue('A1', Str::upper($request->session()->get('institute')));
        $sheet->setCellValue('A2', 'DATA NILAI PELAJARAN');
        $sheet->setCellValue('A4', 'Departemen');
        $sheet->setCellValue('C4', ': '.$request->deptname);
        $sheet->setCellValue('A5', 'Tahun Ajaran');
        $sheet->setCellValue('C5', ': '.$request->school_year);
        $sheet->setCellValue('A6', 'Tingkat/Semester');
        $sheet->setCellValue('C6', ': '.$request->grade.'/'.$request->semester);
        $sheet->setCellValue('A7', 'Guru');
        $sheet->setCellValue('C7', ': '.$request->teacher);
        $sheet->setCellValue('A8', 'Pelajaran');
        $sheet->setCellValue('C8', ': '.$request->lesson);
        $sheet->setCellValue('A9', 'Aspek Penilaian');
        $sheet->setCellValue('C9', ': '.$request->aspect);
        $sheet->setCellValue('A10', 'Jenis Pengujian');
        $sheet->setCellValue('C10', ': '.$request->assessment);
        $sheet->setCellValue('A12', 'NO.');
        $sheet->setCellValue('B12', 'NIS');
        $sheet->setCellValue('C12', 'NAMA');
        foreach ($query_exams as $exam)
        {
            $sheet->setCellValueByColumnAndRow($i + 3, 12, $exam->getLessonExam->code.'-'.$i);
            $sheet->getColumnDimensionByColumn($i + 3)->setWidth(15);
            $i++;
        }
        $sheet->setCellValueByColumnAndRow(4 + count($query_exams), 12, 'RATA-RATA');
        $sheet->setCellValueByColumnAndRow(5 + count($query_exams), 12, 'NILAI AKHIR ' . $request->assessment);
        $sheet->setCellValueByColumnAndRow(6 + count($query_exams), 12, 'PERHITUNGAN');
        $sheet->getColumnDimensionByColumn(4 + count($query_exams))->setWidth(20);
        $sheet->getColumnDimensionByColumn(5 + count($query_exams))->setWidth(20);
        $sheet->getColumnDimensionByColumn(6 + count($query_exams))->setWidth(20);
        //
        $baris = 13;
        $number = 1;
        $scores = json_decode($request->scores);
        foreach ($scores as $key => $score)
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$score->student_no);
            $sheet->setCellValue('C'.$baris,$score->student);
            //
            $x = 0;
            foreach ($score as $k => $v) 
            {
                if (Str::contains($k, Str::lower($request->assessment)))
                {
                    $ids = explode('_', $k);
                    $id = $ids[1];
                    $col = Str::lower($request->assessment).'_'.$id;
                    $sheet->setCellValueByColumnAndRow($x - 3, $baris, $score->{$col});
                }
                $x++;
            }
            //
            $sheet->setCellValueByColumnAndRow(4 + count($query_exams), $baris, $score->avg_score);
            $sheet->setCellValueByColumnAndRow(5 + count($query_exams), $baris, $score->final_score);
            $sheet->setCellValueByColumnAndRow(6 + count($query_exams), $baris, $score->remark);
            $baris++;
            $number++;
        }
        //
        $sheet->setCellValue('A'.$baris = $baris + 1, 'BOBOT NILAI UJIAN');
        $range_start = 'A'.$baris = $baris;
        $range_end = 'C'.$baris = $baris;
        $sheet->getStyle($range_start.':'.$range_end)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->setCellValue('A'.$baris = $baris + 1, $request->assessment);
        $range_start_title = 'A'.$baris = $baris;
        $range_end_title = 'B'.$baris = $baris;
        $sheet->getStyle($range_start_title.':'.$range_end_title)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->setCellValue('C'.$baris, 'BOBOT');
        $sheet->getStyle('C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->mergeCellsByColumnAndRow(1, $baris = $baris - 1, 3, $baris);
        $sheet->mergeCellsByColumnAndRow(1, $baris = $baris + 1, 2, $baris);
        $weights = $this->examEloquent->dataScoreWeight($request);
        $weights_val = 0;
        foreach ($weights as $weight) 
        {
            if ($weight['score'] != null)
            {
                $weights_val = 1;
                $values = explode('<br/>', $weight['assessment']);
                $sheet->setCellValue('A'.$baris = $baris + 1, $values[0]);
                $range_start_sub = 'A'.$baris = $baris;
                $range_end_sub = 'B'.$baris = $baris;
                $sheet->getStyle($range_start_sub.':'.$range_end_sub)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
                $sheet->setCellValue('C'.$baris, $weight['score']);
                $sheet->getStyle('C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
            }
        }
        //
        $index_col = $sheet->getColumnDimensionByColumn(6 + count($query_exams))->getColumnIndex();
        $row = 12;
        $cell = $index_col.$row;
        $range = 'A12:'.$cell;
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A4:C10')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle($range)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        if ($weights_val <> 0)
        {
            $sheet->getStyle('A12:A'.$baris = $baris - 4)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
            $sheet->getStyle('B12:B'.$baris = $baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
            $sheet->getStyle('C12:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        } else {
            $sheet->getStyle('A12:A'.$baris = $baris - 3)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
            $sheet->getStyle('B12:B'.$baris = $baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
            $sheet->getStyle('C12:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        }
        $ranges = 'D13:'.$index_col.$baris = $baris;
        $sheet->getStyle($ranges)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        //
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        // 
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfFormScore(Request $request)
    {
        $data['students'] = Students::select('student_no','name')->where('class_id', $request->class_id)->where('is_active', 1)->get();
        $data['teachers'] = Employee::find($request->employee_id);
        $data['request'] = $request->all();
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.assessments.assessment_lesson_form_score_pdf', $data);
        $name = Str::lower(config('app.name')) .'_form_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfFormScoreFinal(Request $request)
    {
        $grade = DB::table('academic.grades')->select('id')->where('grade', Str::lower($request->grade))->where('department_id',$request->department_id)->first();
        $lesson_exam = DB::table('academic.lesson_exams')->select('code')->where('id', $request->lesson_exam_id)->first();
        $assessment = DB::table('academic.lesson_assessments')->select('id')
                            ->where('employee_id', $request->employee_id)
                            ->where('grade_id', $grade->id)
                            ->where('lesson_id', $request->lesson_id)
                            ->where('score_aspect_id', $request->score_aspect_id)
                            ->where('exam_id', $request->lesson_exam_id)
                            ->first();
        $query_rows = DB::select("SELECT academic.fn_pivotcode_exams('academic.exams_view','student_id','code','score',".$assessment->id.'::int8'.",".$request->class_id.'::int8'.",".$request->semester_id.'::int8'.",'numeric')");
        if ($query_rows[0]->fn_pivotcode_exams != null)
        {
            $data['rows'] = DB::select($query_rows[0]->fn_pivotcode_exams);
            $data['teachers'] = Employee::find($request->employee_id);
            $data['exams'] = Exam::where('lesson_assessment_id', $assessment->id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->get();
            $data['lesson_exam'] = Str::lower($lesson_exam->code);
            $data['request'] = $request->all();
            $data['profile'] = $this->getInstituteProfile();
            //
            $view = View::make('academic::pages.assessments.assessment_lesson_form_score_final_pdf', $data);
            $name = Str::lower(config('app.name')) .'_form_'. Str::of($this->subject_final)->snake();
            $hashfile = md5(date('Ymdhis') . '_' . $name);
            $filename = date('Ymdhis') . '_' . $name . '.pdf';
            // 
            Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
            $this->pdfPortrait($hashfile, $filename);
            $response = [ 'success' => true, 'message' => $filename ];
        } else {
            $response = [ 'success' => false, 'message' => 'Belum ada data ujian pada aspek penilaian: ' . $request->aspect . ' dan jenis pengujian: ' . $request->exam ];
        }
        return response()->json($response);
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfFormScoreReport(Request $request)
    {
        $grade = DB::table('academic.grades')->select('id')->where('grade', Str::lower($request->grade))->where('department_id',$request->department_id)->first();
        $data['teachers'] = Employee::find($request->employee_id);
        $data['assessments'] = LessonAssessment::select('score_aspect_id')
                                ->where('employee_id', $request->employee_id)
                                ->where('grade_id', $grade->id)
                                ->where('lesson_id', $request->lesson_id)
                                ->groupBy('score_aspect_id')
                                ->get();
        $data['assessments_det'] = LessonAssessment::select('academic.lesson_assessments.*',DB::raw('exam_id as exam'))
                                    ->where('employee_id', $request->employee_id)
                                    ->where('grade_id', $grade->id)
                                    ->where('lesson_id', $request->lesson_id)
                                    ->orderBy('exam_id')
                                    ->get();
        $data['students'] = Students::select('id','student_no','name')->where('class_id', $request->class_id)->where('is_active', 1)->get();
        $data['finals'] = DB::table('academic.exam_score_finals')
                            ->where('lesson_id',$request->lesson_id)
                            ->where('class_id',$request->class_id)
                            ->where('semester_id',$request->semester_id)
                            ->get();
        $data['request'] = $request->all();
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.assessments.assessment_lesson_form_score_report_pdf', $data);
        $name = Str::lower(config('app.name')) .'_form_nilai_rapor_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfFormReportComment(Request $request)
    {
        $data['students'] = Students::select('student_no','name')->where('class_id', $request->class_id)->where('is_active', 1)->get();
        $data['teachers'] = Employee::find($request->employee_id);
        $data['request'] = $request->all();
        $data['profile'] = $this->getInstituteProfile();
        //
        $view = View::make('academic::pages.assessments.assessment_lesson_form_comment_report_pdf', $data);
        $name = Str::lower(config('app.name')) .'_form_komen_rapor_santri';
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    // helpers

    private function countAvgScoreStudent($class_id, $semester_id, $assessment_id, $student_id)
    {
        $exams = Exam::select('id','lesson_id','lesson_exam_id')->where('class_id', $class_id)->where('semester_id', $semester_id)->where('lesson_assessment_id', $assessment_id)->get();
        $cnt = 0;
        $score = 0;
        foreach ($exams as $exam) 
        {
            $exam_score = DB::table('academic.exam_scores')->select('score')->where('exam_id', $exam->id)->where('student_id', $student_id)->first()->score;
            $score += $exam_score;
            $cnt++;
        }
        if ($score > 0) 
        {
            $avg_exam_score = round($score/$cnt,2);
        } else {
            $avg_exam_score = round($score,2);
        }
        //
        $lesson_exam = DB::table('academic.lesson_assessments')->select('id','lesson_id','exam_id')->where('id', $assessment_id)->first();
        $getAvgScore = DB::table('academic.avg_score_students')->select('id')
                            ->where('semester_id',$semester_id)
                            ->where('class_id',$class_id)
                            ->where('lesson_exam_id',$lesson_exam->exam_id)
                            ->where('lesson_id',$lesson_exam->lesson_id)
                            ->where('lesson_assessment_id',$assessment_id)
                            ->where('student_id',$student_id)
                            ->first();
        if ($getAvgScore == null)
        {
            DB::table('academic.avg_score_students')->insert([
                'semester_id' => $semester_id,
                'class_id' => $class_id,
                'lesson_exam_id' => $lesson_exam->exam_id,
                'lesson_id' => $lesson_exam->lesson_id,
                'lesson_assessment_id' => $assessment_id,
                'student_id' => $student_id,
                'avg_score' => $avg_exam_score,
                'logged' => auth()->user()->email,
            ]);
        } else {
            DB::table('academic.avg_score_students')->where('id',$getAvgScore->id)->update(['avg_score' => $avg_exam_score, 'logged' => auth()->user()->email]);
        }
    }

    private function countAvgScoreClass($class_id, $semester_id, $assessment_id, $exam_id)
    {
        $exam_score = DB::table('academic.exam_scores')->select('score')->where('exam_id', $exam_id)->get();
        $i = 0;
        $nilai = 0;
        foreach ($exam_score as $val) 
        {
            $nilai += $val->score;
            $i++;
        }
        $avg_exam_score = round($nilai/$i,2);
        //
        $getAvgClass = DB::table('academic.avg_score_classes')->select('id')
                            ->where('semester_id',$semester_id)
                            ->where('class_id',$class_id)
                            ->where('exam_id',$exam_id)
                            ->first();
        if ($getAvgClass === null)
        {
            DB::table('academic.avg_score_classes')->insert([
                'semester_id' => $semester_id,
                'class_id' => $class_id,
                'exam_id' => $exam_id,
                'avg_score' => $avg_exam_score,
                'logged' => auth()->user()->email,
            ]);
        } else {
            DB::table('academic.avg_score_classes')->where('semester_id',$semester_id)->where('class_id',$class_id)->where('exam_id',$exam_id)->update(['avg_score' => $avg_exam_score, 'logged' => auth()->user()->email]);
        }
    }
}
