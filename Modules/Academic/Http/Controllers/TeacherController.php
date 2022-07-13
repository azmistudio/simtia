<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\Teacher;
use Modules\Academic\Repositories\Teacher\TeacherEloquent;
use View;
use Exception;

class TeacherController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Guru Pelajaran';

    function __construct(TeacherEloquent $teacherEloquent)
    {
        $this->teacherEloquent = $teacherEloquent;
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
        $data['status'] = Reference::where('category', 'hr_teacher_status')->get();
        $data['departments'] = $this->listDepartment();
        return view('academic::pages.teachers.teacher', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|int',
            'lesson_id' => 'required|int',
            'status_id' => 'required|int',
        ]);
        try 
        {
            $request->merge([
                'remark' => Str::lower($request->remark),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $this->teacherEloquent->create($request, $this->subject);
            } else {
                $this->teacherEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Pelajaran dan Guru');
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
        return response()->json(Teacher::find($id));
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
            $this->teacherEloquent->destroy($id, $this->subject);
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
        return response()->json($this->teacherEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = $this->teacherEloquent->showIn($idArray);
        //
        $view = View::make('academic::pages.teachers.teacher_pdf', $query);
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
    public function comboGrid(Request $request)
    {
        return response()->json($this->teacherEloquent->combogrid($request));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGridGroup(Request $request)
    {
        return response()->json($this->teacherEloquent->combogridGroup($request));
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function list($id, $department_id)
    {
        return response()->json($this->teacherEloquent->list($id, $department_id));
    }
}
