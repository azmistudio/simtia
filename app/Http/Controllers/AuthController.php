<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\AuditLogTrait;
use App\Repositories\Group\GroupEloquent;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Mobile_Detect;
use Exception;

class AuthController extends Controller
{

    use HelperTrait;
    use AuditLogTrait;

    protected $groupEloquent;

    private $subject_group = 'Data Grup Pengguna';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct(GroupEloquent $groupEloquent)
    {
        $this->groupEloquent = $groupEloquent;
        $this->middleware('permission:utama-grup_pengguna-index', ['only' => ['group']]);
        $this->middleware('permission:utama-grup_pengguna-store', ['only' => ['groupStore']]);
        $this->middleware('permission:utama-grup_pengguna-destroy', ['only' => ['groupDestroy']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->ajax() ? $data['ajax'] = true : $data['ajax'] = false;
        $data['profile'] = $this->getInstituteProfile();
        $request->session()->put('institute', $data['profile']['name']);
        $request->session()->put('institute_logo', $data['profile']['logo']);
        $detect = new Mobile_Detect;
        $data['is_mobile'] = $detect->isMobile();
        if (empty(auth()->user()))
        {
            return view('pages.welcome', $data);
        } else {
            return redirect()->route('home');
        }
    }

    /**
     * Handle an authentication attempt.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);
        if (Auth::attempt($credentials)) 
        {
            $request->session()->regenerate();
            $this->logTransaction($request->email, 'Masuk aplikasi', '{}', '{}');
            $response = $this->getResponse('login');
        } else {
            $response = $this->getResponse('error', 'Kombinasi akun Email dan Kata Sandi tidak ditemukan');
        }
        return response()->json($response);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->logTransaction('#', 'Keluar aplikasi', '{}', '{}');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json($this->getResponse('logout'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function group(Request $request)
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
        return view('pages.user_group', $data);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupShow($id)
    {
        return response()->json($this->groupEloquent->show($id)[0]);
    }

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function groupData(Request $request)
    {
        return response()->json($this->groupEloquent->data($request));
    }    

    /**
     * Display a listing of data.
     * @return JSON
     */
    public function groupPermission()
    {
        $mains = Permission::where('name','like','utama%')->get();
        foreach ($mains as $main) 
        {
            $main_children_name[] = $this->getSingleName($main->name);
            $main_children_action[] = $this->getSingleAction($main->id, $main->name);
        }
        $i = 0;
        foreach (array_unique($main_children_name) as $name) 
        {
            $main_children[] = array(
                'id' => '-2'.$i,
                'name' => $this->getMenuName($name),
                'action' => $this->filterName($name, $main_children_action),
            );
            $i++;
        }
        // 
        $masters = Permission::where('name','like','data_master%')->get();
        foreach ($masters as $master) 
        {
            $master_children_name[] = $this->getSingleName($master->name);
            $master_children_action[] = $this->getSingleAction($master->id, $master->name);
        }
        $j = 0;
        foreach (array_unique($master_children_name) as $name) 
        {
            $master_children[] = array(
                'id' => '-3'.$j,
                'name' => $this->getMenuName($name),
                'action' => $this->filterName($name, $master_children_action),
            );
            $j++;
        }
        // 
        $academics = Permission::where('name','like','akademik%')->get();
        foreach ($academics as $academic) 
        {
            $academic_children_name[] = $this->getSingleName($academic->name);
            $academic_children_action[] = $this->getSingleAction($academic->id, $academic->name);
        }
        $j = 0;
        foreach (array_unique($academic_children_name) as $name) 
        {
            $academic_children[] = array(
                'id' => '-3'.$j,
                'name' => $this->getMenuName($name),
                'action' => $this->filterName($name, $academic_children_action),
            );
            $j++;
        }
        // 
        $accountings = Permission::where('name','like','keuangan%')->get();
        foreach ($accountings as $accounting) 
        {
            $accounting_children_name[] = $this->getSingleName($accounting->name);
            $accounting_children_action[] = $this->getSingleAction($accounting->id, $accounting->name);
        }
        $j = 0;
        foreach (array_unique($accounting_children_name) as $name) 
        {
            $accounting_children[] = array(
                'id' => '-3'.$j,
                'name' => $this->getMenuName($name),
                'action' => $this->filterName($name, $accounting_children_action),
            );
            $j++;
        }
        
        $result[] = array(
            'id' => '-1',
            'name' => 'Semua Menu &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" onclick="checkAllMenu(true)">Centang semua menu</a>&nbsp;|&nbsp;<a href="javascript:void(0)" onclick="checkAllMenu(false)">Batal</a>',
            'action' => '',
            'children' => array(
                [
                    'id' => '-2',
                    'name' => 'Menu Utama &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($main_children_name)).'" onclick="checkSubMenu(this, true)">Centang semua sub menu</a>&nbsp;|&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($main_children_name)).'" onclick="checkSubMenu(this, false)">Batal</a>',
                    'action' => '',
                    'children' => $main_children,
                ],
                [
                    'id' => '-3',
                    'name' => 'Menu Data Master &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($master_children_name)).'" onclick="checkSubMenu(this, true)">Centang semua sub menu</a>&nbsp;|&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($master_children_name)).'" onclick="checkSubMenu(this, false)">Batal</a>',
                    'action' => '',
                    'children' => $master_children,
                ],
                [
                    'id' => '-4',
                    'name' => 'Menu Akademik &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($academic_children_name)).'" onclick="checkSubMenu(this, true)">Centang semua sub menu</a>&nbsp;|&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($academic_children_name)).'" onclick="checkSubMenu(this, false)">Batal</a>',
                    'action' => '',
                    'children' => $academic_children,
                ],
                [
                    'id' => '-5',
                    'name' => 'Menu Keuangan &nbsp;&nbsp;&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($accounting_children_name)).'" onclick="checkSubMenu(this, true)">Centang semua sub menu</a>&nbsp;|&nbsp;<a href="javascript:void(0)" id="'.implode(",", array_unique($accounting_children_name)).'" onclick="checkSubMenu(this, false)">Batal</a>',
                    'action' => '',
                    'children' => $accounting_children,
                ],
            )
        );
        // response
        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function groupStore(Request $request)
    {
        //
        $validated = $request->validate([
            'name' => 'required|string',
        ]);
        try 
        {
            $request->merge([
                'name' => Str::title($request->name)
            ]);
            if ($request->id < 1) 
            {
                $action = 'Tambah'; 
                $this->groupEloquent->create($request, $this->subject_group);
            } else {
                $action = 'Ubah Simpan';
                $this->groupEloquent->update($request, $this->subject_group);
            }
            $response = $this->getResponse('store', '', $this->subject_group);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function groupDestroy($id)
    {
        try 
        {
            $this->groupEloquent->destroy($id, $this->subject_group);
            $response = $this->getResponse('destroy', '', $this->subject_group);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage());
        }
        return response()->json($response);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function expired()
    {
        //
        return view('errors.419');
    }

    // helpers
    private function filterName($name, $array)
    {
        $values = '<div class="perms-checkbox" style="top:3px;position:relative;"><a href="javascript:void(0)" id="'.$name.'" onclick="checkDetailMenu(this, true)">Centang</a>&nbsp;|&nbsp;<a href="javascript:void(0)" id="'.$name.'" onclick="checkDetailMenu(this, false)">Batal</a>&nbsp;&nbsp;&nbsp;';
        foreach (Arr::sort($array) as $value) 
        {
            $val = explode("-", $value);
            if ($val[1] == $name) {
                $values .= '<input type="checkbox" name="permissions[]" id="permission'.$val[0].'" value="'.$val[0].'" class="'.$val[1].'" style="top:3px;position:relative;"><label style="top:2px;position:relative;">&nbsp;'.$this->getActionName($val[2]).'</label>&nbsp;&nbsp;&nbsp;';
            }
        }
        $values .= '</div>';
        return $values;
    }


    private function getSingleName($param)
    {
        $name = explode('-', $param);
        return $name[1];
    }

    private function getSingleAction($id, $param)
    {
        $name = explode('-', $param);
        return $id .'-'. $name[1] .'-'. $name[2];
    }

    private function getMenuName($param)
    {
        return Str::title(Str::replace('_',' ',$param));
    }

    private function getActionName($param)
    {
        $value = explode('::', $param);
        switch ($value[0]) {
            case 'store':
                return 'Tambah/Ubah Data';
                break;
            case 'destroy':
                return 'Hapus Data';
                break;
            default:
                return 'Akses Menu';
                break;
        }
    }

}
