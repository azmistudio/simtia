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
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Entities\LessonExam;
use Modules\Academic\Http\Requests\LessonExamTypeRequest;
use Modules\Academic\Repositories\Lesson\LessonExamTypeEloquent;
use View;
use Exception;

class LessonExamTypeController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Jenis Pengujian';

    function __construct(LessonExamTypeEloquent $lessonExamTypeEloquent)
    {
        $this->lessonExamTypeEloquent = $lessonExamTypeEloquent;
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
        return view('academic::pages.lessons.lesson_exam', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(LessonExamTypeRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'code' => Str::lower($request->code),
                'subject' => Str::lower($request->subject),
                'remark' => Str::lower($request->remark),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                if ($request->has('is_all')) 
                {
                    $lessons = Lesson::where('department_id', $request->department_id)->where('is_active', 1)->get();
                    foreach ($lessons as $lesson) 
                    {
                        $request->merge([
                            'lesson_id' => $lesson->id
                        ]);
                        $this->lessonExamTypeEloquent->create($request, $this->subject);
                    }
                } else {
                    $this->lessonExamTypeEloquent->create($request, $this->subject);
                }
            } else {
                $this->lessonExamTypeEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Pelajaran dan Singkatan');
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
        return response()->json(LessonExam::find($id));
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
            $this->lessonExamTypeEloquent->destroy($id, $this->subject);
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
        return response()->json($this->lessonExamTypeEloquent->data($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function list($id, $aspect_id)
    {
        return response()->json($this->lessonExamTypeEloquent->list($id, $aspect_id));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->lessonExamTypeEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.lessons.lesson_exam_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of combobox.
     * @return JSON
     */
    public function comboBox($id)
    {
        return response()->json($this->lessonExamTypeEloquent->combobox($id));
    }
}
