<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Traits\HelperTrait;
use App\Models\User;
use App\Models\Department;
use App\Models\Notification;
use Modules\Academic\Entities\SchoolYear;
use Modules\Academic\Entities\Semester;
use App\Repositories\Notification\NotificationEloquent;
use ConnectException;
use Carbon\Carbon;
use Parsedown;

class HomeController extends Controller
{

    use HelperTrait;

    function __construct(NotificationEloquent $notificationEloquent)
    {
        $this->notificationEloquent = $notificationEloquent;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->ajax() ? $data['ajax'] = true : $data['ajax'] = false;
        $department = User::where('email', auth()->user()->email)->first();
        // get active schoolyear period
        $q_schoolyear = SchoolYear::where('is_active', 1)->first();
        $schoolyear = !is_null($q_schoolyear) ? $q_schoolyear->school_year : '-';
        $request->session()->put('schoolyear', $schoolyear);
        // get active semester
        $q_semester = Semester::select('semester')->where('is_active', 1)->get()->pluck('semester')->toArray();
        $semester = !is_null($q_semester) ? implode(', ',$q_semester) : '-';
        $request->session()->put('semester', $semester);
        // check update
        $this->checkUpdate();
        //
        $data['notification'] = Notification::where('user_id', auth()->user()->id)->where('is_read', 0)->count();
        $data['total_dept'] = Department::where('is_all', 0)->count();
        $data['department'] = $department->getDepartment;
        $data['dateNow'] = Carbon::now()->isoFormat('dddd, D MMM Y');
        $data['dateHijriah'] = date('d-m-Y');
        return view('pages.home', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
    {
        if (!$request->ajax())
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;

        return view('errors.under', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function manual(Request $request)
    {
        if (!$request->ajax())
        {
            abort(404);
        }
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;

        return view('pages.manual', $data);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function about(Request $request)
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
        $Parsedown = new Parsedown();
        $data['version'] = config('app.version');
        $data['release_desc'] =  $Parsedown->text($this->getConfigs('app_version', config('app.version'))->value);
        return view('pages.about', $data);
    }

    /**
     * Get Hijri date.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getHijri()
    {
        $datenow = date('d-m-Y');
        try {
            $request = Http::get('http://api.flagodna.com/hijriyah/api/' . $datenow);
            $response = [ 'success' => true, 'message' => $request->json()[0]['tanggal_hijriyah'] . ' ' . $request->json()[0]['bulan_hijriyah'] . ' ' . $request->json()[0]['tahun_hijriyah'] ];
        } catch (ConnectException $e) {
            $response = [ 'success' => false, 'message' => $e->getMessage() ];
        }
        return response()->json($response);
    }

    public function fileDownload($file)
    {
        $filepath = storage_path('app/public/downloads/' . $file);
        return response()->download($filepath, basename($file));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function notifications(Request $request)
    {
        $data['lists'] = Notification::where('user_id', auth()->user()->id)->where('is_read', 0)->get();
        Notification::where('user_id', auth()->user()->id)->update(['is_read' => 1]);
        return view('pages.notification', $data);
    }
}
