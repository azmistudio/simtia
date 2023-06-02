<?php

namespace Modules\Finance\Repositories\Reference;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\BookYear;
use Carbon\Carbon;

class BookYearEloquent implements BookYearRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return BookYear::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['start_from','book_year','start_date','number','end_date','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        //
        return BookYear::where('id', $payload['id'])->update($payload);
	}

	public function data(Request $request)
	{
		$param = $this->gridRequest($request, 'desc', 'id');
        $query = BookYear::select('*');
        $bookyear = isset($request->params['fbookyear']) ? $request->params['fbookyear'] : '';
        if ($bookyear != '')
        {
            $query = $query->where('book_year', $bookyear);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['period'] = $this->formatDate($model->start_date,'local') .' - '. $this->formatDate($model->end_date,'local');
            $model['book_year'] = $model->is_active == 1 ? '<b>'.$model->book_year .' (A)</b>' : $model->book_year;
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
	{
		$request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return BookYear::destroy($id);
	}

	public function combogrid(Request $request)
    {
        $query = BookYear::where('is_active', 1);
        $result["rows"] = $query->orderBy('book_years.id')->get()->map(function ($model) {
            $model['department_id'] = $model->department_id;
            $model['start'] = $this->formatDate($model->start_date,'local');
            $model['department'] = $model->getDepartment->name;
            return $model;
        });
        return $result;
    }

	private function logActivity(Request $request, $model_id, $subject, $action)
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'book_year' => $request->book_year,
				'start_date' => $request->start_date,
				'end_date' => $request->end_date,
				'prefix' => $request->prefix,
				'number' => $request->number,
				'is_active' => $request->is_active,
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = BookYear::find($model_id);
			$before = array(
				'book_year' => $query->book_year,
				'start_date' => $query->start_date,
				'end_date' => $query->end_date,
				'prefix' => $query->prefix,
				'number' => $query->number,
				'is_active' => $query->is_active,
			);
			$after = array(
				'book_year' => $request->has('book_year') ? $request->book_year : $query->book_year,
				'start_date' => $request->has('start_date') ? $request->start_date : $query->start_date,
				'end_date' => $request->has('end_date') ? $request->end_date : $query->end_date,
				'prefix' => $request->has('prefix') ? $request->prefix : $query->prefix,
				'number' => $request->has('number') ? $request->number : $query->number,
				'is_active' => $request->has('is_active') ? $request->is_active : $query->is_active,
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
