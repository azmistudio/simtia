<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;
use App\Models\Department;
use App\Http\Requests\DepartmentRequest;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\ReferenceTrait;
use App\Repositories\Department\DepartmentEloquent;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use View;
use Exception;

class DepartmentController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use ReferenceTrait;

    private $subject = 'Data Departemen';

    function __construct(DepartmentEloquent $departmentEloquent)
    {
        $this->departmentEloquent = $departmentEloquent;
        $this->middleware('permission:data_master-departemen-index', ['only' => ['index']]);
        $this->middleware('permission:data_master-departemen-store', ['only' => ['store']]);
        $this->middleware('permission:data_master-departemen-destroy', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
        return view('pages.department', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DepartmentRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'name' => Str::lower($request->name),
                'is_active' => $request->is_active ?: 1,
                'is_all' => 0,
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1)
            {
                $this->departmentEloquent->create($request, $this->subject);
            } else {
                $this->departmentEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Nama Departemen');
        }
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return response()->json(Department::find($id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try 
        {
            $this->departmentEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Departemen');
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->departmentEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function toPdf(Request $request)
    {
        $idArray = collect(json_decode($request->data))->pluck('id')->toArray();
        $query['models'] = Department::whereIn('id', $idArray)->orderBy('id')->get()->map(function ($model) {
            $model['is_active'] = $this->getActive()[$model->is_active];
            return $model;
        });
        // 
        $view = View::make('pages.department_pdf', $query);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }
}
