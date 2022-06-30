<?php

namespace Modules\HR\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Reference;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\HR\Entities\Employee;
use Modules\HR\Repositories\HR\HREloquent;
use Spatie\Permission\Models\Role;
use Exception;
use View;

class ProfileController extends Controller
{
    use HelperTrait;
    use ReferenceTrait;

    function __construct(HREloquent $HREloquent)
    {
        $this->HREloquent = $HREloquent;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        //
        if (!$request->ajax()) 
        {
            abort(404);
        }
        // request
        $window = explode(".", $request->w);
        $data['InnerHeight'] = $window[0];
        $data['InnerWidth'] = $window[1];
        $data['ViewType'] = $request->t;
        // data
        $data['sections'] = Reference::where('category', 'hr_section')->get();
        $data['tribes'] = Reference::where('category', 'hr_tribe')->get();
        $data['profile'] = Employee::where('email', auth()->user()->email)->first();
        $data['roles'] = Role::select('id','name')->get();
        $data['user'] = User::where('email', auth()->user()->email)->first();
        $data['user_role'] = DB::table('model_has_roles')->select('role_id','model_id')->where('model_id', $data['user']->id)->first();
        return view('hr::pages.profile', $data);
    }
}
