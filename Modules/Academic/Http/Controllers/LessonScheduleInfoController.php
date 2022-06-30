<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\LessonScheduleInfo;
use Modules\Academic\Repositories\Lesson\LessonScheduleInfoEloquent;
use View;
use Exception;

class LessonScheduleInfoController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;

    private $subject = 'Data Info Jadwal';

    function __construct(LessonScheduleInfoEloquent $lessonScheduleInfoEloquent)
    {
        $this->lessonScheduleInfoEloquent = $lessonScheduleInfoEloquent;
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
        return view('academic::pages.lessons.lesson_schedule_info');
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function list($id)
    {
        return response()->json($this->lessonScheduleInfoEloquent->list($id));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function store(Request $request, $id)
    {
        $validated = $request->validate([
            'description' => 'required|string',
        ]);
        try
        {
            $request->merge([
                'schoolyear_id' => $id,
                'description' => Str::lower($request->description),
                'is_active' => $request->is_active == "Tidak" ? 1 : 2,
                'logged' => auth()->user()->email,
            ]);
            if (isset($request->isNewRecord)) 
            {
                $this->lessonScheduleInfoEloquent->create($request, $this->subject);
            } else {
                $this->lessonScheduleInfoEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request)
    {
        try 
        {
            $this->lessonScheduleInfoEloquent->destroy($request, $this->subject);
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
    public function data(Request $request, $id)
    {
        return response()->json($this->lessonScheduleInfoEloquent->data($request, $id));
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function comboGrid(Request $request)
    {
        return response()->json($this->lessonScheduleInfoEloquent->combogrid($request));
    }
}
