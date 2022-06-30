<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\Reference;
use App\Http\Requests\ReferenceRequest;
use App\Repositories\Reference\ReferenceEloquent;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Exception;

class ReferenceController extends Controller
{

    use HelperTrait;
    use ReferenceTrait;

    private $subject = 'Data Referensi Sistem';

    function __construct(ReferenceEloquent $referenceEloquent)
    {
        $this->referenceEloquent = $referenceEloquent;
        $this->middleware('permission:data_master-referensi_sistem-index', ['only' => ['index']]);
        $this->middleware('permission:data_master-referensi_sistem-store', ['only' => ['store']]);
        $this->middleware('permission:data_master-referensi_sistem-destroy', ['only' => ['destroy']]);
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
        $data['references'] = Arr::except($this->getCategory(), ['hr_student_mutation']);
        return view('pages.reference', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function store(ReferenceRequest $request, $id)
    {
        $validated = $request->validated();
        try 
        {
            if ($id < 1 && $request->isNewRecord != null)
            {
                $this->referenceEloquent->create($request, $this->subject);        
            } else {
                $this->referenceEloquent->update($request, $this->subject);        
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of data for combogrid.
     * @return JSON
     */
    public function list(Request $request, $category)
    {
        if (!$request->ajax()) 
        {
            abort(404);
        }
        // query
        $query = Reference::where('category', $category)->get()->pluck('id','name');
        $options[] = array('id' => '', 'name' => '---');
        foreach ($query as $row) 
        {
            $options[] = array(
                'id' => $row->id,
                'name' => $row->name
            );
        }
        return response()->json($options);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        return response()->json($this->referenceEloquent->data($request));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try 
        {
            $this->referenceEloquent->destroy($id, $this->subject);
            $response = $this->getResponse('destroy', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage(), $this->subject);
        }
        return response()->json($response);
    }
}
