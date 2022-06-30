<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Institute;
use App\Models\Department;
use App\Http\Requests\InstituteRequest;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\PdfTrait;
use App\Http\Traits\DepartmentTrait;
use App\Repositories\Institute\InstituteEloquent;
use View;
use Exception;

class InstituteController extends Controller
{
    use HelperTrait;
    use PdfTrait;
    use DepartmentTrait;

    private $subject = 'Data Profil Lembaga';

    function __construct(InstituteEloquent $instituteEloquent)
    {
        $this->instituteEloquent = $instituteEloquent;
        $this->middleware('permission:data_master-profil_lembaga-index', ['only' => ['index']]);
        $this->middleware('permission:data_master-profil_lembaga-store', ['only' => ['store']]);
        $this->middleware('permission:data_master-profil_lembaga-destroy', ['only' => ['destroy']]);
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
        //
        $data['depts'] = $this->allDepartment();
        return view('pages.institute', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(InstituteRequest $request)
    {
        $validated = $request->validated();
        if ($request->file('logo')) 
        {
            $imagePath = $request->file('logo');
            $imageName = date('YmdHis') . '-' . $this->filter_filename($imagePath->getClientOriginalName());
            $path = $request->file('logo')->storeAs('uploads', $imageName, 'public');
        }
        try 
        {
            $request->merge([
                'website' => Str::lower($request->website),
                'email' => Str::lower($request->email),
                'logged' => auth()->user()->email,
            ]);
            if ($request->id < 1) 
            {
                $request->merge([
                    'logo' => isset($imageName) ? $imageName : '',
                ]);
                $this->instituteEloquent->create($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            } else {
                if (isset($imageName)) {
                    $request->merge([
                        'logo' => $imageName
                    ]);
                }
                $this->instituteEloquent->update($request, $this->subject);
                $response = $this->getResponse('store', '', $this->subject);
            }
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), 'Nama Lembaga');
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
        return response()->json(Institute::find($id));
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
            $this->instituteEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->instituteEloquent->data($request));
    }

    /**
     * Export resource to PDF Document.
     * @return PDF
     */
    public function print(Request $request)
    {
        $payload = json_decode($request->data);
        $data['rows'] = Institute::find($payload->id);
        //
        $view = View::make('pages.institute_pdf', $data);
        $name = Str::lower(config('app.name')) .'_'. Str::of($this->subject)->snake();
        $hashfile = md5(date('Ymdhis') . '_' . $name);
        $filename = date('Ymdhis') . '_' . $name . '.pdf';
        // 
        Storage::disk('local')->put('public/tempo/'.$hashfile . '.html', $view->render());
        $this->pdfPortrait($hashfile, $filename);
        echo $filename;
    }
}
