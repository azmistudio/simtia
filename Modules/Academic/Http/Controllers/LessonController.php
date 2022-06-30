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
use Modules\Academic\Entities\LessonGroup;
use Modules\Academic\Repositories\Lesson\LessonEloquent;
use View;
use Exception;

class LessonController extends Controller
{

    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject_score = 'Data Aspek Penilaian';
    private $subject_group = 'Data Kelompok Pelajaran';

    function __construct(LessonEloquent $lessonEloquent)
    {
        $this->lessonEloquent = $lessonEloquent;
    }

    // score aspect

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
        return view('academic::pages.lessons.lesson_reference', $data);
    }

    // Score Aspect

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeScoreAspect(Request $request)
    {
        $validated = $request->validate([
            'basis' => 'required|string',
            'remark' => 'required|string',
        ]);
        try 
        {
            $request->merge([
                'basis' => Str::lower($request->basis),
                'remark' => Str::lower($request->remark),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $this->lessonEloquent->createScoreAspect($request, $this->subject_score);
            } else {
                $this->lessonEloquent->updateScoreAspect($request, $this->subject_score);
            }
            $response = $this->getResponse('store', '', $this->subject_score);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kode Aspek Penilaian');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showScoreAspect($id)
    {
        return response()->json(ScoreAspect::find($id));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyScoreAspect($id)
    {
        try 
        {
            $this->lessonEloquent->destroyScoreAspect($id, $this->subject_score);
            $response = $this->getResponse('destroy', '', $this->subject_score);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Aspek Penilaian');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataScoreAspect(Request $request)
    {
        return response()->json($this->lessonEloquent->dataScoreAspect($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfScoreAspect(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = ScoreAspect::whereIn('id', $idArray)->orderBy('id')->get();
        //
        $view = View::make('academic::pages.academics.score_aspect_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_score)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    // lesson group

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function storeLessonGroup(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'group' => 'required|string',
            'order' => 'required|int',
        ]);
        try 
        {
            $request->merge([
                'code' => Str::lower($request->code),
                'group' => Str::lower($request->group),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $this->lessonEloquent->createLessonGroup($request, $this->subject_group);
            } else {
                $this->lessonEloquent->updateLessonGroup($request, $this->subject_group);
            }
            $response = $this->getResponse('store', '', $this->subject_group);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kode dan Urutan');
        }
        return response()->json($response);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function showLessonGroup($id)
    {
        return response()->json(LessonGroup::find($id));
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroyLessonGroup($id)
    {
        try 
        {
            $this->lessonEloquent->destroyLessonGroup($id, $this->subject_group);
            $response = $this->getResponse('destroy', '', $this->subject_group);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kelompok Pelajaran');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataLessonGroup(Request $request)
    {
        return response()->json($this->lessonEloquent->dataLessonGroup($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfLessonGroup(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = LessonGroup::whereIn('id', $idArray)->orderBy('id')->get();
        //
        $view = View::make('academic::pages.lessons.lesson_group_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_group)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }
}
