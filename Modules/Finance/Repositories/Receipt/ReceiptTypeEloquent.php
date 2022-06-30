<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\ReceiptType;
use Modules\Finance\Entities\BookYear;
use Carbon\Carbon;

class ReceiptTypeEloquent implements ReceiptTypeRepository
{

	use AuditLogTrait;
    use HelperTrait;

	public function create(Request $request, $subject)
    {
        $payload = $request->all();
        $this->logActivity($request, 0, $subject, 'Tambah');
        return ReceiptType::create($payload);
    }

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['is_all','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return ReceiptType::where('id', $payload['id'])->update($payload);
    }

    public function data(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = ReceiptType::select('*');
        // filter
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        $dept = isset($request->params['fdept']) ? $request->params['fdept'] : '';
        if ($dept != '') 
        {
            $query = $query->where('department_id', $dept);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function($model) {
            $model['department_id'] = $model->getDepartment->name;
            return $model;
        });
        return $result;
    }

    public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return ReceiptType::destroy($id);
    }
	
    public function combobox($id, $department_id)
    {
        $query = ReceiptType::select('id','name')->where('category_id', $id)->where('department_id', $department_id)->orderBy('id')->get();
        $results[] = array('id' => 0, 'name' => '---');
        foreach ($query as $val) 
        {
            $results[] = array(
                'id' => $val->id,
                'name' => $val->name,
            );
        }
        return $results;
    }

    public function combogrid(Request $request)
    {
        $page = isset($request->page) ? intval($request->page) : 1;
        $rows = isset($request->rows) ? intval($request->rows) : 10;
        $query = ReceiptType::where('category_id', $request->category_id);
        // filter
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($filter).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($page - 1) * $rows)->take($rows)->orderBy('id')->get()->map(function ($model) {
            $model['department_id'] = $model->department_id;
            $model['department'] = $model->getDepartment->name;
            return $model->only(['id','department_id','name','department']);
        });
        return $result;
    }

    public function combogridPayment(Request $request)
    {
        $department_id = isset($request->department_id) ? $request->department_id : -1;
        $is_mandatory = isset($request->is_mandatory) ? $request->is_mandatory : -1;
        $query = ReceiptType::select('*');
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('department_id', auth()->user()->department_id);
        } 
        // filter
        $filter = isset($request->q) ? $request->q : '';
        if ($filter != '') 
        {
            $query = $query->whereRaw('LOWER(name) like ?', ['%'.Str::lower($filter).'%']);
        }
        if ($department_id > 0)
        {
            $query = $query->where('department_id', $department_id);
        }
        if ($is_mandatory > 0)
        {
            $query = $query->whereIn('category_id', [1,2]);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->orderBy('id')->get()->map(function ($model) {
            $model['department_id'] = $model->department_id;
            $model['department'] = $model->getDepartment->name;
            $model['category'] = $model->getCategory->category;
            $model['category_id'] = $model->getCategory->id;
            return $model->only(['id','department_id','name','department','category','category_id']);
        });
        return $result;
    }

    public function search($category, $department_id)
    {
        return ReceiptType::whereHas('getCategory',function($qry) use($category){
                    $qry->where('code',$category);
                })
                ->where('department_id',$department_id)
                ->where('is_active',1)
                ->get();
    }

    private function logActivity(Request $request, $model_id, $subject, $action) 
    {
        if ($action == 'Tambah')
        {
            $data = array(
                'department_id' => $request->department_id, 
                'category_id' => $request->category_id, 
                'name' => $request->name, 
                'cash_account' => $request->cash_account, 
                'receipt_account' => $request->receipt_account, 
                'receivable_account' => $request->receivable_account, 
                'discount_account' => $request->discount_account, 
            );
            $this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
        } else {
            $query = ReceiptType::find($model_id);
            $before = array(
                'department_id' => $query->department_id, 
                'category_id' => $query->category_id, 
                'name' => $query->name, 
                'cash_account' => $query->cash_account, 
                'receipt_account' => $query->receipt_account, 
                'receivable_account' => $query->receivable_account, 
                'discount_account' => $query->discount_account, 
            );
            $after = array(
                'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
                'category_id' => $request->has('category_id') ? $request->category_id : $query->category_id, 
                'name' => $request->has('name') ? $request->name : $query->name, 
                'cash_account' => $request->has('cash_account') ? $request->cash_account : $query->cash_account, 
                'receipt_account' => $request->has('receipt_account') ? $request->receipt_account : $query->receipt_account, 
                'receivable_account' => $request->has('receivable_account') ? $request->receivable_account : $query->receivable_account, 
                'discount_account' => $request->has('discount_account') ? $request->discount_account : $query->discount_account, 
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