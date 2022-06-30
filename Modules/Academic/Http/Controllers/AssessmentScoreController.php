<?php

namespace Modules\Academic\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\HelperTrait;
use View;
use Exception;

class AssessmentScoreController extends Controller
{
    use HelperTrait;

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
        $data['users'] = DB::table('academic.audit_exam_scores')
                            ->select('academic.audit_exam_scores.logged','users.name')
                            ->join('users','users.email','=','academic.audit_exam_scores.logged')
                            ->groupBy('academic.audit_exam_scores.logged','users.name')
                            ->get();
        $data['years'] = DB::table('academic.audit_exam_scores')->select(DB::raw("to_char(date_trans, 'YYYY') as years"))->groupByRaw(1)->get();
        return view('academic::pages.assessments.assessment_score_audit', $data);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function data(Request $request)
    {
        // request
        $page = isset($request->page) ? intval($request->page) : 1;
        $rows = isset($request->rows) ? intval($request->rows) : 10;
        $sort = isset($request->sort) ? $request->sort : 'id';
        $order = isset($request->order) ? $request->order : 'desc';
        // query
        $query = DB::table('academic.audit_exam_scores')->select('*',DB::raw("to_char(date_trans, 'DD/MM/YYYY HH:MI:SS') as timestamp"))->orderByDesc('date_trans');
        // filter
        $fuser = isset($request->fuser) ? $request->fuser : '';
        if ($fuser != '') 
        {
            $query = $query->where('logged', Str::lower($fuser));
        }
        $fmonth = isset($request->fmonth) ? $request->fmonth : '';
        $fyear = isset($request->fyear) ? $request->fyear : '';
        if ($fmonth != '' && $fyear != '') 
        {
            $query = $query->whereMonth('date_trans', $fmonth);
            $query = $query->whereYear('date_trans', $fyear);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($page - 1) * $rows)->take($rows)->orderBy($sort, $order)->get();
        return response()->json($result);
    }
}
