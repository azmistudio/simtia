<?php

namespace App\Repositories\Reference;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Models\Reference;
use Carbon\Carbon;

class ReferenceEloquent implements ReferenceRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$lastOrder = Reference::where('category', $request->category)->orderByDesc('id')->pluck('order')->first();
        $newOrder = !empty($lastOrder) ? intval($lastOrder) + 1 : 1;
		// 
		$payload = $request->all();
		$payload['order'] = $newOrder;
		$payload['code'] = Str::upper($request->code) ?: '-';
		$payload['remark'] = $request->remark ?: '-';
		$payload['parent'] = $request->parent ?: 0;
		// 
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Reference::create($payload);
	}

	public function update(Request $request, $subject)
	{
        $payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $request->id, $subject, 'Ubah');
        return Reference::where('id', $request->id)->update($payload);
	}

	public function show($search, $is_one)
	{
		$query = Reference::select('*');
		foreach ($search as $val) 
		{
			switch ($val['action']) 
			{
				case 'like':
					$query = $query->where($val['column'], 'like', '%'.Str::lower($val['query']).'%');
					break;
				default:
					$query = $query->where($val['column'], $val['query']);
					break;
			}
		}
		return $is_one == true ? $query->first() : $query->get(); 
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request, 'asc');
        $query = Reference::select('*');
        // filter
        $category = $request->has('fcategory') ? $request->fcategory : '';
        if ($category != '') 
        {
            $query = $query->where('category', $category);
        } else {
            $query = $query->where('category', 'hr_job');
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
		return Reference::destroy($id);
	}
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'code' => $request->code, 
				'name' => $request->name, 
				'category' => $request->category, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Reference::find($model_id);
			$before = array(
				'code' => $query->code, 
				'name' => $query->name, 
				'category' => $query->category, 
			);
			$after = array(
				'code' => $request->has('code') ? $request->code : $query->code, 
				'name' => $request->has('name') ? $request->name : $query->name, 
				'category' => $request->has('category') ? $request->category : $query->category, 
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