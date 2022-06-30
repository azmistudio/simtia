<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\DepartmentTrait;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    use HelperTrait;
    use DepartmentTrait;

    function __construct()
    {
        $this->middleware('permission:utama-log_aplikasi-index', ['only' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
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
        $data['users'] = AuditLog::select('audit_logs.user','users.name')->join('users','users.email','=','audit_logs.user')->groupBy('audit_logs.user','users.name')->get();
        return view('pages.audit_log', $data);
    }
    
    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        $param = $this->gridRequest($request);
        $query = AuditLog::orderByDesc('created_at');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        // filter
        $fdepartment = isset($request->fdepartment) ? $request->fdepartment : '';
        if ($fdepartment != '') 
        {
            $query = $query->where('department_id', $fdepartment);
        }
        $fuser = isset($request->fuser) ? $request->fuser : '';
        if ($fuser != '') 
        {
            $query = $query->where('user', Str::lower($fuser));
        }
        $fstart = isset($request->fstart) ? $request->fstart : '';
        $fend = isset($request->fend) ? $request->fend : '';
        if ($fstart != '' && $fend != '') 
        {
            $query = $query->whereDate('created_at', '>=', $this->formatDate($fstart,'sys'));
            $query = $query->whereDate('created_at', '<=', $this->formatDate($fend,'sys'));
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model){
            $model['created'] = $this->formatDate($model->created_at,'localtimes');
            $model['before'] = $this->jsonToList($model->before);
            $model['after'] = $this->jsonToList($model->after);
            return $model;
        });
        return response()->json($result);
    }

    // helper 

    private function jsonToList($param)
    {
        $response = json_decode($param, true);
        if (count($response) > 0)
        {
            $result = '<ul style="padding-left:15px;margin-bottom:0px;">';
            foreach ($response as $key => $value) 
            {
                $result .= '<li>'.$key.': '.$value.'</li>';
            }
            $result .= '</ul>';
            return $result;
        } else {
            return '-';
        }
    }
}
