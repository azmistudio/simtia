<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\File;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use Modules\Academic\Entities\Lesson;
use Modules\Academic\Entities\LessonPlan;
use Modules\Academic\Http\Requests\LessonPlanRequest;
use Modules\Academic\Repositories\Lesson\LessonPlanEloquent;
use View;
use Exception;

class LessonPlanController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject = 'Data Rencana Program Pembelajaran';

    function __construct(LessonPlanEloquent $lessonPlanEloquent)
    {
        $this->lessonPlanEloquent = $lessonPlanEloquent;
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
        $data['lessons'] = Lesson::select('id','name')->where('is_active', 1)->get();
        return view('academic::pages.lessons.lesson_plan', $data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(LessonPlanRequest $request)
    {
        $validated = $request->validated();
        $files = array();
        if ($request->file('lesson_plan_file')) 
        {
            foreach ($request->file('lesson_plan_file') as $file) 
            {
                $imageName = date('YmdHis') . '-' . $this->filter_filename($file->getClientOriginalName());
                $path = $file->storeAs('uploads/lesson_plan', $imageName, 'public');
                $files[] = array(
                    'original_name' => $file->getClientOriginalName(),
                    'extension' => $file->getClientOriginalExtension(),
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'name' => $imageName
                );
            }
        }
        try 
        {
            $request->merge([
                'code' => Str::lower($request->code),
                'subject' => Str::lower($request->subject),
                'is_active' => $request->is_active ?: 1,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $query = $this->lessonPlanEloquent->create($request, $this->subject);
                if (count($files) > 0)
                {
                    foreach ($files as $file) 
                    {
                        $assets = new File();
                        $assets->department_id = $request->department_id;
                        $assets->source_id = $query->id;
                        $assets->source_name = 'lesson_plan';
                        $assets->original_name = $file['original_name'];
                        $assets->extension = $file['extension'];
                        $assets->size = $file['size'];
                        $assets->mime = $file['mime'];
                        $assets->name = $file['name'];
                        $assets->created_at = date('Y-m-d H:i:s');
                        $assets->save();
                    }
                }
            } else {
                $action = 'Ubah Simpan';
                $this->lessonPlanEloquent->update($request, $this->subject);
                if (count($files) > 0)
                {
                    File::where('source_id', $request->id)->where('source_name', 'lesson_plan')->delete();
                    foreach ($files as $file) 
                    {
                        $assets = new File();
                        $assets->department_id = $request->department_id;
                        $assets->source_id = $request->id;
                        $assets->source_name = 'lesson_plan';
                        $assets->original_name = $file['original_name'];
                        $assets->extension = $file['extension'];
                        $assets->size = $file['size'];
                        $assets->mime = $file['mime'];
                        $assets->name = $file['name'];
                        $assets->created_at = date('Y-m-d H:i:s');
                        $assets->save();
                    }
                }
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Kombinasi Departemen, Tingkat, Semester, Pelajaran dan Kode RPP');
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
        $lesson_plan = LessonPlan::find($id);
        $files = File::where('source_id', $id)->where('source_name','lesson_plan')->get();
        return response()->json(array(
            'id' => $lesson_plan->id,
            'department_id' => $lesson_plan->department_id,
            'grade_id' => $lesson_plan->grade_id,
            'semester_id' => $lesson_plan->semester_id,
            'lesson_id' => $lesson_plan->lesson_id,
            'code' => $lesson_plan->code,
            'subject' => $lesson_plan->subject,
            'description' => $lesson_plan->description,
            'is_active' => $lesson_plan->is_active,
            'files' => $files,
        ));
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
            $this->lessonPlanEloquent->destroy($id, $this->subject);
            File::where('source_id', $id)->where('source_name', 'lesson_plan')->delete();
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
        return response()->json($this->lessonPlanEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $vals['models'] = $this->lessonPlanEloquent->showIn($idArray);
        $vals['profile'] = $this->getInstituteProfile();
        // 
        $view = View::make('academic::pages.lessons.lesson_plan_pdf', $vals);
        $name = Str::lower(config('app.name')) .'_'. Str::of(Str::lower($this->subject))->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortraits($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of combobox.
     * @return JSON
     */
    public function comboBox(Request $request)
    {
        return response()->json($this->lessonPlanEloquent->combobox($request));
    }
}
