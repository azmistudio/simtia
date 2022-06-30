<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Traits\DepartmentTrait;
use App\Http\Traits\HelperTrait;
use App\Repositories\User\UserEloquent;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Exception;

class UserController extends Controller
{

    use DepartmentTrait;
    use HelperTrait;

    private $subject = 'Data Pengguna';

    function __construct(UserEloquent $userEloquent)
    {
        $this->userEloquent = $userEloquent;
        $this->middleware('permission:utama-pengguna-index', ['only' => ['index']]);
        $this->middleware('permission:utama-pengguna-store', ['only' => ['store']]);
        $this->middleware('permission:utama-pengguna-destroy', ['only' => ['destroy']]);
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
        $data['roles'] = Role::select('id','name')->get();
        $data['departments'] = $this->allDepartment();
        return view('pages.user', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $validated = $request->validated();
        try 
        {
            $request->merge([
                'name' => Str::lower($request->name),
                'email' => Str::lower($request->email),
            ]);
            if ($request->id < 1)
            {
                $this->userEloquent->create($request, $this->subject);
            } else {
                $this->userEloquent->update($request, $this->subject);
            }
            $response = $this->getResponse('store', '', $this->subject);
        } catch (\Throwable $e) {
            $response = $this->getResponse('error', $e->getMessage());
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
        $query = User::join('model_has_roles', 'id', '=', 'model_has_roles.model_id')->select('users.*','role_id as roles')->find($id);
        return response()->json($query);
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
            $this->userEloquent->destroy($id, $this->subject);
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
    public function data(Request $request)
    {
        return response()->json($this->userEloquent->data($request));
    }
}
