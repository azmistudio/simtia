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
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\LessonGrading;
use Modules\Academic\Entities\LessonAssessment;
use Modules\Academic\Entities\Students;
use Modules\Academic\Entities\ExamReport;
use Modules\Academic\Entities\ExamScoreFinal;
use Modules\Academic\Entities\ExamReportScoreInfo;
use Modules\Academic\Entities\ExamReportScoreFinal;
use Modules\Academic\Http\Requests\ExamReportRequest;
use Modules\Academic\Repositories\Lesson\LessonAssessmentEloquent;
use Modules\Academic\Repositories\Lesson\LessonGradingEloquent;
use Modules\Academic\Repositories\Exam\ExamReportEloquent;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Carbon\Carbon;
use View;
use Exception;

class AssessmentReportController extends Controller
{

    use HelperTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Perhitungan Nilai Rapor';

    function __construct(
        LessonAssessmentEloquent $lessonAssessmentEloquent,
        LessonGradingEloquent $lessonGradingEloquent,
        ExamReportEloquent $examReportEloquent
    )
    {
        $this->lessonAssessmentEloquent = $lessonAssessmentEloquent;
        $this->lessonGradingEloquent = $lessonGradingEloquent;
        $this->examReportEloquent = $examReportEloquent;
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
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.assessments.assessment_report', $data);
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function loadScore(Request $request)
    {
        if (!$request->ajax())
        {
            abort(404);
        }
        $data['requests'] = $request->all();
        $data['assessments'] = $this->lessonAssessmentEloquent->show($request->employee_id, $request->grade_id, $request->lesson_id, $request->score_aspect)->sortBy('exam_id');
        $data['value_letters'] = $this->lessonGradingEloquent->show($request->employee_id, $request->grade_id, $request->lesson_id, $request->score_aspect)->sortBy('id')->where('grade','<>','');
        return view('academic::pages.assessments.assessment_report_score', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(ExamReportRequest $request)
    {
        $validated = $request->validated();
        try
        {
            // check number
            for ($i=0; $i < count($request->students); $i++)
            {
                if (!isset($request->students[$i]['value']))
                {
                    throw new Exception('Nilai Angka wajib diisi (minimal 0).', 1);
                }
                if (!isset($request->students[$i]['value_letter']))
                {
                    throw new Exception('Nilai Huruf wajib dipilih.', 1);
                }
            }
            //
            $request->merge([
                'value' => 1,
                'logged' => auth()->user()->email,
            ]);
            // get assessment_id
            $q_assessment = LessonAssessment::select('id')->where('employee_id', $request->employee_id)->where('grade_id', $request->grade_id)->where('lesson_id', $request->lesson_id)->where('score_aspect_id', $request->score_aspect_id)->orderBy('id')->first();
            if ($request->id < 1)
            {
                $query = $this->examReportEloquent->create($request, $this->subject);
                $q_kkm = ExamReportScoreInfo::upsert([
                    [
                        'exam_report_id' => $query->id,
                        'lesson_id' => $request->lesson_id,
                        'class_id' => $request->class_id,
                        'semester_id' => $request->semester_id,
                        'value' => $request->value,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                ],['exam_report_id','lesson_id','class_id','semester_id'],['value','updated_at']);
                $q_kkm_id = DB::table('academic.exam_report_score_infos')->select('id')->where('lesson_id', $request->lesson_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->first();
                for ($i=0; $i < count($request->students); $i++)
                {
                    DB::table('academic.exam_report_score_finals')->insert(
                        [
                            'exam_report_id' => $query->id,
                            'exam_report_info_id' => $q_kkm_id->id,
                            'student_id' => $request->students[$i]['id'],
                            'lesson_assessment_id' => $q_assessment->id,
                            'value' => $request->students[$i]['value'],
                            'value_letter' => $request->students[$i]['value_letter'],
                            'created_at' => date('Y-m-d H:i:s'),
                        ]
                    );
                }
                $response = $this->getResponse('store', '', $this->subject);
            } else {
                $q_kkm = ExamReportScoreInfo::upsert([
                    [
                        'exam_report_id' => $request->id,
                        'lesson_id' => $request->lesson_id,
                        'class_id' => $request->class_id,
                        'semester_id' => $request->semester_id,
                        'value' => $request->value,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]
                ],['exam_report_id','lesson_id','class_id','semester_id'],['value','updated_at']);
                $q_kkm_id = DB::table('academic.exam_report_score_infos')->select('id')->where('lesson_id', $request->lesson_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->first();
                for ($i=0; $i < count($request->students); $i++)
                {
                    DB::table('academic.exam_report_score_finals')
                        ->where('exam_report_id', $request->id)
                        ->where('exam_report_info_id', $q_kkm_id->id)
                        ->where('student_id', $request->students[$i]['id'])
                        ->where('lesson_assessment_id', $q_assessment->id)
                        ->update([
                            'value' => $request->students[$i]['value'],
                            'value_letter' => $request->students[$i]['value_letter'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
                $this->examReportEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Aspek Penilaian');
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
        return response()->json(ExamReport::where('id', $id)->get()->map(function($model) {
            $model['department'] = $model->getSemester->getDepartment->name;
            $model['grade'] = $model->getClass->getGrade->grade;
            $model['grade_id'] = $model->getClass->grade_id;
            $model['school_year'] = $model->getClass->getSchoolYear->school_year;
            $model['semester'] = $model->getSemester->semester;
            $model['class'] = $model->getClass->class;
            $model['lesson'] = $model->getLesson->name;
            $model['employee'] = $this->getEmployeeName($model->employee_id);
            $model['remark'] = $model->getScoreAspect->remark;
            // $model['value'] = isset($model->getExamReportScoreInfo) ? $model->getExamReportScoreInfo->value : '-';
            return $model;
        })[0]);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try
        {
            $this->examReportEloquent->destroy($id, $this->subject);
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
        return response()->json($this->examReportEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function list(Request $request)
    {
        $exam_report_ids = ExamReport::select('id')->where('lesson_id', $request->lesson_id)->where('class_id', $request->class_id)->where('semester_id', $request->semester_id)->where('employee_id', $request->employee_id)->get();
        if (count($exam_report_ids) > 0)
        {
            foreach ($exam_report_ids as $val)
            {
                $idArray[] = $val->id;
            }
            $query = $this->examReportEloquent->list($request, $idArray);
        } else {
            $query = array();
        }
        return response()->json($query);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->examReportEloquent->comboGrid($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataScore(Request $request)
    {
        $q_students = Students::select('id','student_no','name')->where('class_id', $request->class_id)->where('is_active', 1)->get();
        $q_finals = ExamScoreFinal::select('academic.exam_score_finals.*','academic.lesson_exams.code')
                        ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exam_score_finals.lesson_exam_id')
                        ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_score_finals.lesson_assessment_id')
                        ->where('academic.exam_score_finals.lesson_id',$request->lesson_id)
                        ->where('academic.exam_score_finals.class_id',$request->class_id)
                        ->where('academic.exam_score_finals.semester_id',$request->semester_id)
                        ->where('academic.lesson_assessments.score_aspect_id',$request->score_aspect)
                        ->get();
        $q_report = ExamReportScoreFinal::select('student_id','value','value_letter')->where('exam_report_id', $request->exam_report_id)->get();
        $students = array();
        $exams = array();
        $columns = array();
        foreach ($q_students as $student)
        {
            $students[] = array(
                'id' => $student->id,
                'student_no' => $student->student_no,
                'student' => $student->name,
            );
            foreach ($q_finals as $final)
            {
                if ($final->student_id == $student->id)
                {
                    for ($i = 0; $i < count($request->data); $i++)
                    {
                        if ($final->lesson_exam_id == $request->data[$i])
                        {
                            foreach ($students as $key => $value)
                            {
                                if ($final->student_id == $value['id'])
                                {
                                    $students[$key][$final->code] = number_format($final->score,2);
                                    $value = $this->getSumScore($request->employee_id, $request->grade_id, $request->lesson_id, $request->score_aspect, $final->student_id, $request->semester_id);
                                    $students[$key]['value'] = number_format($value,2);
                                    $students[$key]['value_letter'] = $this->getLetterScore($request->employee_id, $request->grade_id, $request->lesson_id, $request->score_aspect, $value);
                                }
                            }
                        }
                    }
                }
            }
            foreach ($students as $key => $value)
            {
                if (count($q_report) > 0)
                {
                    foreach ($q_report as $report)
                    {
                        if ($report->student_id == $value['id'])
                        {
                            $students[$key]['value'] = number_format($report->value,2);
                            $students[$key]['value_letter'] = $report->value_letter;
                        }
                    }
                }
            }
        }
        $result["rows"] = $students;
        return response()->json($result);
    }

    /**
     * Show the specified resource.
     * @param Request $request
     * @return Renderable
     */
    public function showInfoValue(Request $request)
    {
        $query = DB::table('academic.exam_report_score_infos')
            ->where('lesson_id', $request->lesson_id)
            ->where('class_id', $request->class_id)
            ->where('semester_id', $request->semester_id)
            ->first();
        echo isset($query) ? $query->value : 0;
    }

    /**
     * Export resource to Excel Document.
     * @return Excel
     */
    public function toExcel(Request $request)
    {
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
        $sheet->setCellValue('A2', 'PERHITUNGAN NILAI RAPOR');
        $sheet->setCellValue('A4', 'Kelas');
        $sheet->setCellValue('C4', ': '.$request->class);
        $sheet->setCellValue('A5', 'Pelajaran');
        $sheet->setCellValue('C5', ': '.$request->lesson);
        $sheet->setCellValue('A6', 'Aspek Pengujian');
        $sheet->setCellValue('C6', ': '.$request->score_aspect);
        $sheet->setCellValue('A8', 'NO.');
        $sheet->setCellValue('B8', 'NIS');
        $sheet->setCellValue('C8', 'NAMA');
        //
        $q_assessment = $this->lessonAssessmentEloquent->show($request->employee_id, $request->grade_id, $request->lesson_id, $request->score_aspect_id)->sortBy('exam_id');
        $q_finals = ExamScoreFinal::select('academic.exam_score_finals.*','academic.lesson_exams.code')
                        ->join('academic.lesson_exams','academic.lesson_exams.id','=','academic.exam_score_finals.lesson_exam_id')
                        ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_score_finals.lesson_assessment_id')
                        ->where('academic.exam_score_finals.lesson_id',$request->lesson_id)
                        ->where('academic.exam_score_finals.class_id',$request->class_id)
                        ->where('academic.exam_score_finals.semester_id',$request->semester_id)
                        ->get();
        $i = 0;
        $array_col = array();
        foreach ($q_assessment as $assessment)
        {
            $sheet->setCellValueByColumnAndRow($i + 4, 8, $assessment->getLessonExam->code.' ('.$assessment->value.')');
            $sheet->getColumnDimensionByColumn($i + 4)->setWidth(20);
            $array_col[$i++] = array($assessment->exam_id, $i+3);
        }
        $sheet->setCellValueByColumnAndRow(4 + count($q_assessment), 8, 'ANGKA');
        $sheet->setCellValueByColumnAndRow(5 + count($q_assessment), 8, 'HURUF');
        $sheet->getColumnDimensionByColumn(4 + count($q_assessment))->setWidth(15);
        $sheet->getColumnDimensionByColumn(5 + count($q_assessment))->setWidth(15);
        //
        $baris = 9;
        $number = 1;
        $scores = json_decode($request->scores);
        foreach ($scores as $key => $score)
        {
            $sheet->setCellValue('A'.$baris,$number);
            $sheet->setCellValue('B'.$baris,$score->student_no);
            $sheet->setCellValue('C'.$baris,$score->student);
            $x = 1;
            foreach ($q_finals as $final)
            {
                if ($final->student_id == $score->id)
                {
                    foreach ($array_col as $value)
                    {
                        if ($final->lesson_exam_id == $value[0])
                        {
                            $col = $final->code;
                            if (isset($score->{$col}))
                            {
                                $sheet->setCellValueByColumnAndRow($value[1], $baris, $score->{$col});
                            }
                        }
                    }
                }
                $x++;
            }
            $scoreval = isset($score->value) ? number_format($score->value, 2) : '0';
            $scorelet = isset($score->value_letter) ? $score->value_letter : '-';
            $sheet->setCellValueByColumnAndRow(4 + count($q_assessment), $baris, $scoreval);
            $sheet->setCellValueByColumnAndRow(5 + count($q_assessment), $baris, $scorelet);
            $baris++;
            $number++;
        }
        //
        $index_col = $sheet->getColumnDimensionByColumn(5 + count($q_assessment))->getColumnIndex();
        $row = 8;
        $cell = $index_col.$row;
        $range = 'A8:'.$cell;
        $sheet->getStyle('A1:A2')->applyFromArray($this->PHPExcelCommonStyle()['title']);
        $sheet->getStyle('A4:C6')->applyFromArray($this->PHPExcelCommonStyle()['subTitle']);
        $sheet->getStyle($range)->applyFromArray($this->PHPExcelCommonStyle()['header']);
        $sheet->getStyle('A8:A'.$baris = $baris - 1)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('B8:B'.$baris = $baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        $sheet->getStyle('C8:C'.$baris)->applyFromArray($this->PHPExcelCommonStyle()['bodyLeft']);
        $ranges = 'D9:'.$index_col.$baris = $baris;
        $sheet->getStyle($ranges)->applyFromArray($this->PHPExcelCommonStyle()['bodyCenter']);
        //
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $filename = date('Ymdhis') . '_' . $name . '.xlsx';
        //
        $writer = new Xlsx($spreadsheet);
        $writer->save('storage/downloads/'.$filename);
        echo $filename;
    }

    // helpers

    private function getSumScore($employee_id, $grade_id, $lesson_id, $score_aspect_id, $student_id, $semester_id)
    {
        $score = ExamScoreFinal::select(DB::raw('SUM((academic.exam_score_finals.score * academic.lesson_assessments.value)) as total'))
                    ->join('academic.lesson_assessments','academic.lesson_assessments.id','=','academic.exam_score_finals.lesson_assessment_id')
                    ->where('academic.exam_score_finals.lesson_id', $lesson_id)
                    ->where('academic.exam_score_finals.student_id', $student_id)
                    ->where('academic.exam_score_finals.semester_id', $semester_id)
                    ->where('academic.lesson_assessments.score_aspect_id', $score_aspect_id)
                    ->first()->total;
        $weight = LessonAssessment::where('employee_id', $employee_id)
                    ->where('grade_id', $grade_id)
                    ->where('lesson_id', $lesson_id)
                    ->where('score_aspect_id', $score_aspect_id)
                    ->sum('value');

        return $score / $weight;
    }

    private function getLetterScore($employee_id, $grade_id, $lesson_id, $score_aspect_id, $value)
    {
        $grade = '';
        $query = $this->lessonGradingEloquent->show($employee_id, $grade_id, $lesson_id, $score_aspect_id)->sortBy('id')->where('grade','<>','');
        foreach ($query as $val)
        {
            if ($value >= $val->min && $value <= $val->max)
            {
                $grade = $val->grade;
            }
        }
        return $grade;
    }
}
