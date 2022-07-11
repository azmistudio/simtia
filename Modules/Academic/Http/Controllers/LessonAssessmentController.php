<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\ScoreAspect;
use Modules\Academic\Entities\Grade;
use Modules\Academic\Entities\LessonExam;
use Modules\Academic\Entities\LessonAssessment;
use Modules\Academic\Http\Requests\LessonAssessmentRequest;
use Modules\Academic\Repositories\Lesson\LessonAssessmentEloquent;
use View;
use Exception;

class LessonAssessmentController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Aturan Nilai Rapor';

    function __construct(lessonAssessmentEloquent $lessonAssessmentEloquent)
    {
        $this->lessonAssessmentEloquent = $lessonAssessmentEloquent;
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
        $data['scores'] = ScoreAspect::select('id','basis','remark')->get();
        $data['exams'] = LessonExam::select('id','subject')->get();
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.lessons.lesson_assesment', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(LessonAssessmentRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            if (isset($request->valueopts))
            {
                if ($request->id < 1) 
                {
                    $this->lessonAssessmentEloquent->create($request, $this->subject);
                } else {
                    $this->lessonAssessmentEloquent->update($request, $this->subject);
                }
                $response = $this->getResponse('store', '', $this->subject);
            } else {
                throw new Exception('Belum ada data Jenis Pengujian, silahkan tambah data Jenis Pengujian terlebih dahulu.', 1);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Guru, Tingkat, Aspek Penilaian');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($employee_id, $grade_id, $lesson_id, $score_aspect_id)
    {
        return response()->json($this->lessonAssessmentEloquent->show($employee_id, $grade_id, $lesson_id, $score_aspect_id));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($employee_id, $grade_id, $lesson_id, $score_aspect_id)
    {
        try 
        {
            $this->lessonAssessmentEloquent->destroy($employee_id, $grade_id, $lesson_id, $score_aspect_id, $this->subject);
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
        return response()->json($this->lessonAssessmentEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $employeeIdArray = collect(json_decode($request->data))->pluck('employee')->toArray();
        $gradeIdArray = collect(json_decode($request->data))->pluck('grade')->toArray();
        $lessonIdArray = collect(json_decode($request->data))->pluck('lesson')->toArray();
        $scoreAspectIdArray = collect(json_decode($request->data))->pluck('score_aspect')->toArray();
        $vals['groups'] = $this->lessonAssessmentEloquent->dataGroupIn($employeeIdArray, $gradeIdArray, $lessonIdArray, $scoreAspectIdArray);
        $vals['models'] = $this->lessonAssessmentEloquent->showIn($employeeIdArray, $gradeIdArray, $lessonIdArray, $scoreAspectIdArray);
        // 
        $view = View::make('academic::pages.lessons.lesson_assesment_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        //
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function combobox(Request $request)
    {
        $query = $this->lessonAssessmentEloquent->combobox($request);
        $result = array();
        foreach ($query as $val) 
        {
            $result[] = array(
                'id' => $val->id.'-'.$val->exam_id.'-'.$val->score_aspect_id,
                'text' => strtoupper($val->subject),
                'group' => $val->score_aspect
            );
        }
        return response()->json($result);
    }
}
