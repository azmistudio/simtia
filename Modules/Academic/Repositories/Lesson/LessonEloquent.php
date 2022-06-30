<?php

namespace Modules\Academic\Repositories\Lesson;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use App\Http\Traits\ReferenceTrait;
use Modules\Academic\Entities\ScoreAspect;
use Modules\Academic\Entities\LessonGroup;
use Carbon\Carbon;

class LessonEloquent implements LessonRepository
{
	use AuditLogTrait;
	use HelperTrait;
	use ReferenceTrait;
	
	// score aspect

	public function createScoreAspect(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah', 'score');
		return ScoreAspect::create($payload);
	}

	public function updateScoreAspect(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah', 'score');
        // 
        return ScoreAspect::where('id', $payload['id'])->update($payload);
	}

	public function dataScoreAspect(Request $request)
	{
        $param = $this->gridRequest($request);
        $query = ScoreAspect::select('*');
        // result
        $result["total"] = $query->distinct()->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
	}

	public function destroyScoreAspect($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus', 'score');
		return ScoreAspect::destroy($id);
	}

	// lesson group

	public function createLessonGroup(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah', 'group');
		return LessonGroup::create($payload);
	}

	public function updateLessonGroup(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah', 'group');
        // 
        return LessonGroup::where('id', $payload['id'])->update($payload);
	}

	public function dataLessonGroup(Request $request)
	{
		$param = $this->gridRequest($request);
        $query = LessonGroup::select('*');
        // result
        $result["total"] = $query->distinct()->count('id');
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
	}

	public function destroyLessonGroup($id, $subject)
	{
		$request = new Request();
		$this->logActivity($request, $id, $subject, 'Hapus', 'group');
		return LessonGroup::destroy($id);
	}

	private function logActivity(Request $request, $model_id, $subject, $action, $object) 
	{
		if ($action == 'Tambah')
		{
			switch ($object) 
			{
				case 'group':
					$data = array(
						'code' => $request->code, 
						'group' => $request->group, 
						'order' => $request->order, 
					);
					break;
				default:
					$data = array(
						'basis' => $request->basis, 
						'remark' => $request->remark, 
					);
					break;
			}
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			switch ($object) 
			{
				case 'group':
					$query = LessonGroup::find($model_id);
					$before = array(
						'group' => $query->group, 
						'code' => $query->code, 
						'order' => $query->order, 
					);
					$after = array(
						'group' => $request->has('group') ? $request->group : $query->group, 
						'code' => $request->has('code') ? $request->code : $query->code, 
						'order' => $request->has('order') ? $request->order : $query->order, 
					);
					break;
				default:
					$query = ScoreAspect::find($model_id);
					$before = array(
						'basis' => $query->basis, 
						'remark' => $query->remark, 
						'capacity' => $query->capacity, 
					);
					$after = array(
						'basis' => $request->has('basis') ? $request->basis : $query->basis, 
						'remark' => $request->has('remark') ? $request->remark : $query->remark, 
					);
					break;
			}
			if ($action == 'Ubah')
			{
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), json_encode($after));
			} else {
		        $this->logTransaction('#', $action .' '. $subject, json_encode($before), '{}');
			}
		} 
	}
}