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
use Modules\Academic\Http\Requests\LessonTimeRequest;
use Modules\Academic\Entities\LessonScheduleTime;
use Modules\Academic\Repositories\Lesson\LessonTimeEloquent;
use View;
use Exception;

class LessonTimeController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Jam Belajar';

    function __construct(LessonTimeEloquent $lessonTimeEloquent)
    {
        $this->lessonTimeEloquent = $lessonTimeEloquent;
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
        $data['depts'] = $this->listDepartment();
        return view('academic::pages.lessons.lesson_time', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(LessonTimeRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                if ($request->start == $request->end)
                {
                    throw new Exception('Waktu Mulai dan Selesai tidak boleh sama.', 1);
                } else {
                    $this->lessonTimeEloquent->create($request, $this->subject);
                    $response = $this->getResponse('store', '', $this->subject);
                }
            } else {
                $this->lessonTimeEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Departemen dan Jam Ke');
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
        return response()->json(LessonScheduleTime::find($id));
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
            $this->lessonTimeEloquent->destroy($id, $this->subject);
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
        return response()->json($this->lessonTimeEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->lessonTimeEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.lessons.lesson_time_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function combobox($id)
    {
        return response()->json($this->lessonTimeEloquent->combobox($id));
    }
}
