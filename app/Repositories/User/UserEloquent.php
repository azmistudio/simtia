<?php

namespace App\Repositories\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Models\User;
use Hash;

class UserEloquent implements UserRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$query = new User;
		$query->name = $request->name;
		$query->password = Hash::make($request->password);
		$query->email = $request->email;
		$query->department_id = $request->department_id;
		$query->save();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return $query->assignRole($request->input('roles'));
	}

	public function update(Request $request, $subject)
	{
		$query = User::find($request->id);
		$query->name = $request->name;
		if ($request->password != 'default-no-change')
		{
			$query->password = Hash::make($request->password);
		}
		$query->email = $request->email;
		$query->department_id = $request->department_id;
		$query->save();
		DB::table('model_has_roles')->where('model_id',$request->id)->delete();
		$this->logActivity($request, $request->id, $subject, 'Ubah');
		return $query->assignRole($request->input('roles'));
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request);
		$query = User::select('*');
		// filter
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->where('name', 'like', '%'.Str::lower($name).'%');
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return User::destroy($id);
	}
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'name' => $request->name, 
				'email' => $request->email, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = User::find($model_id);
			$before = array(
				'name' => $query->name, 
				'email' => $query->email, 
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
				'email' => $request->has('email') ? $request->email : $query->email, 
			);
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		} 
	}
}