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
use Modules\Academic\Http\Requests\LessonDataRequest;
use Modules\Academic\Entities\LessonGroup;
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Repositories\Lesson\LessonDataEloquent;
use View;
use Exception;

class LessonDataController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Pelajaran';

    function __construct(LessonDataEloquent $lessonDataEloquent)
    {
        $this->lessonDataEloquent = $lessonDataEloquent;
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
        $data['groups'] = LessonGroup::select('id','group')->get();
        return view('academic::pages.lessons.lesson', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(LessonDataRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'code' => Str::lower($request->code),
                'name' => Str::lower($request->name),
                'remark' => Str::lower($request->remark),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                if ($request->has('is_all')) 
                {
                    foreach ($this->listDepartment() as $dept) 
                    {
                        $request->merge([
                            'department_id' => $dept->id
                        ]);
                        $this->lessonDataEloquent->create($request, $this->subject);
                    }
                } else {
                    $this->lessonDataEloquent->create($request, $this->subject);
                }
            } else {
                $this->lessonDataEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Singkatan');
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
        return response()->json(Lesson::find($id));
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
            $this->lessonDataEloquent->destroy($id, $this->subject);
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
        return response()->json($this->lessonDataEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->lessonDataEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.lessons.lesson_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfLandscape($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->lessonDataEloquent->combogrid($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGridTeacher(Request $request)
    {
        return response()->json($this->lessonDataEloquent->combogridTeacher($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboBox($id)
    {
        return response()->json($this->lessonDataEloquent->combobox($id));
    }
}
