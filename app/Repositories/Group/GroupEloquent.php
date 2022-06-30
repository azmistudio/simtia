<?php

namespace App\Repositories\Group;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\AuditLogTrait;

class GroupEloquent implements GroupRepository
{

	use HelperTrait;
	use AuditLogTrait;

	public function create(Request $request, $subject)
	{
		$query = new Role;
		$query->name = $request->name;
		$query->save();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return $query->syncPermissions($request->input('permissions'));
	}

	public function update(Request $request, $subject)
	{
		$query = Role::find($request->id);
		$query->name = $request->name;
		$query->save();
		$this->logActivity($request, $request->id, $subject, 'Ubah');
		return $query->syncPermissions($request->input('permissions'));
	}

	public function show($id)
	{
		return DB::table('roles')
				->select('id','name', DB::raw("(SELECT array_to_string(ARRAY(SELECT b.permission_id FROM roles a LEFT JOIN role_has_permissions b ON a.id = b.role_id WHERE a.id = ".$id."), ',')) AS permissions"))
				->where('id', $id)
				->get();
	}

	public function data(Request $request)
	{
        $param = $this->gridRequest($request);
        $query = Role::select('id', 'name');
        //
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus');
		return DB::table("roles")->where('id',$id)->delete();
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'name' => $request->name, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Role::find($model_id);
			$before = array(
				'name' => $query->name, 
			);
			$after = array(
				'name' => $request->has('name') ? $request->name : $query->name, 
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