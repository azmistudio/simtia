<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use App\Models\Room;
use App\Http\Requests\RoomRequest;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Http\Traits\DepartmentTrait;
use App\Repositories\General\RoomEloquent;
use View;
use Exception;

class GeneralController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;
    use DepartmentTrait;

    private $subject_student_room = 'Data Kamar Santri';

    function __construct(RoomEloquent $roomEloquent)
    {
        $this->roomEloquent = $roomEloquent;
        $this->middleware('permission:data_master-kamar-index', ['only' => ['indexRoom']]);
        $this->middleware('permission:data_master-kamar-store', ['only' => ['storeRoom']]);
        $this->middleware('permission:data_master-kamar-destroy', ['only' => ['destroyRoom']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexRoom(Request $request, $subject)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        if ($subject == 'student')
        {
            $data['departments'] = $this->allDepartment();
            return view('pages.room_student', $data);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRoom(RoomRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'name' => Str::lower($request->name),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1)
            {
                $this->roomEloquent->create($request, $this->subject_student_room);
            } else {
                $this->roomEloquent->update($request, $this->subject_student_room);
            }
            $response = $this->getResponse('store', '', $this->subject_student_room);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Nama Kamar');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function showRoom($id)
    {
        return response()->json(Room::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyRoom($id)
    {
        try 
        {
            $this->roomEloquent->destroy($id, $this->subject_student_room);
            $response = $this->getResponse('destroy', '', $this->subject_student_room);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function dataRoom(Request $request)
    {
        return response()->json($this->roomEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdfRoom(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = Room::whereIn('id', $idArray)->orderBy('id')->get()->map(function ($model) {
            $model['department'] = $model->getDepartment->name;
            $model['gender'] = $model->gender == 1 ? 'Ikhwan' : 'Akhwat';
            $model['capacity'] = $model->capacity .'/'. $model->getOccupied($model->id);
            return $model;
        });
        // 
        $view = View::make('pages.room_student_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject_student_room)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function comboGridRoom(Request $request)
    {
        return response()->json($this->roomEloquent->combogrid($request));
    }

    // quran surah

    /**
     * Display a listing of data for combobox.
     * @return JSON
     */
    public function comboBoxSurah()
    {
        return response()->json(DB::table('quran_surahs')->selectRaw('id, surah')->orderBy('id')->get());
    }
}
