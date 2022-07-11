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
use Modules\Academic\Entities\LessonGrading;
use Modules\Academic\Http\Requests\LessonGradingRequest;
use Modules\Academic\Repositories\Lesson\LessonGradingEloquent;
use View;
use Exception;

class LessonGradingController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Aturan Grading';

    function __construct(lessonGradingEloquent $lessonGradingEloquent)
    {
        $this->lessonGradingEloquent = $lessonGradingEloquent;
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
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.lessons.lesson_grading', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(LessonGradingRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            if ($request->id < 1) 
            {
                $action = 'Tambah'; 
                $checkAspect = LessonGrading::select('employee_id','grade_id','lesson_id','score_aspect_id')
                                ->whereRaw(
                                    'EXISTS (
                                        SELECT 1 FROM academic.lesson_gradings a
                                        WHERE a.employee_id = ? AND a.grade_id = ? AND a.lesson_id = ? AND a.score_aspect_id = ?
                                        GROUP BY a.employee_id, a.grade_id, a.lesson_id, a.score_aspect_id
                                    )',[$request->employee_id, $request->grade_id, $request->lesson_id, $request->score_aspect_id])
                                ->get();
                if (count($checkAspect) > 0)
                {
                    throw new Exception('Kombinasi Guru, Tingkat, Pelajaran dan Aspek Penilaian sudah digunakan.', 1);
                } else {
                    if ($request->has('is_all')) 
                    {
                        $score_aspects = ScoreAspect::get();
                        foreach ($score_aspects as $score) 
                        {
                            $request->merge([
                                'score_aspect_id' => $score->id
                            ]);
                            $this->lessonGradingEloquent->create($request, $this->subject);
                        }
                    } else {
                        $this->lessonGradingEloquent->create($request, $this->subject);
                    }
                    $response = $this->getResponse('store', '', $this->subject);
                }
            } else {
                $this->lessonGradingEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
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
        return response()->json($this->lessonGradingEloquent->show($employee_id, $grade_id, $lesson_id, $score_aspect_id));
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
            $this->lessonGradingEloquent->destroy($employee_id, $grade_id, $lesson_id, $score_aspect_id, $this->subject);
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
        return response()->json($this->lessonGradingEloquent->data($request));
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
        $vals['groups'] = $this->lessonGradingEloquent->dataGroupIn($employeeIdArray, $gradeIdArray, $lessonIdArray, $scoreAspectIdArray);
        $vals['models'] = $this->lessonGradingEloquent->showIn($employeeIdArray, $gradeIdArray, $lessonIdArray, $scoreAspectIdArray);
        // 
        $view = View::make('academic::pages.lessons.lesson_grading_pdf', $vals);
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
    public function combobox($employee_id, $grade_id, $lesson_id)
    {
        $grade = Grade::select('id')->where('grade', Str::lower($grade_id))->first();
        $query = LessonGrading::select(DB::raw('score_aspect_id as id'),'score_aspect_id')
                    ->where('employee_id', $employee_id)
                    ->where('grade_id', $grade->id)
                    ->where('lesson_id', $lesson_id)
                    ->groupBy('score_aspect_id')
                    ->get();
        $result = array();
        foreach ($query as $val) 
        {
            $result[] = array(
                'id' => $val->id,
                'text' => $val->score_aspect_id,
            );
        }
        return response()->json($result);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboboxGrade(Request $request)
    {
        $query = LessonGrading::select('id','grade')
                    ->where('employee_id', $request->employee_id)
                    ->where('grade_id', $request->grade_id)
                    ->where('lesson_id', $request->lesson_id)
                    ->where('score_aspect_id', $request->score_aspect)
                    ->where('grade','<>','')
                    ->orderBy('id')
                    ->get();
        $result = array();
        foreach ($query as $val) 
        {
            $result[] = array(
                'id' => $val->grade,
                'text' => $val->grade,
            );
        }
        return response()->json($result);
    }
}
