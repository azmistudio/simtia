<?php

namespace Modules\Finance\Repositories\Receipt;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\PaymentMajor;
use Modules\Finance\Entities\ReceiptMajor;
use Carbon\Carbon;

class PaymentMajorEloquent implements PaymentMajorRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return PaymentMajor::create($payload);
	}

    public function update(Request $request, $subject)
    {
        $payload = Arr::except($request->all(), ['is_all','created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return PaymentMajor::where('id', $payload['id'])->update($payload);
    }

	public function data(Request $request)
	{
        $param = $this->gridRequest($request, 'asc', 'id');
        $query = PaymentMajor::select('*');
        // filter
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
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get()->map(function ($model) {
            $model['department'] = $model->getDepartment->name;
            return $model;
        });
        return $result;
	}

	public function destroy($id, $subject)
    {
        $request = new Request();
        $this->logActivity($request, $id, $subject, 'Hapus');
        return PaymentMajor::destroy($id);
    }

    public function dataStudent(Request $request)
    {
        $param = $this->gridRequest($request, 'asc', 'finance.payment_major_students_view.student_id');
        $query = DB::table('finance.payment_major_students_view')->select(
			            'finance.payment_major_students_view.*',
			            DB::raw('UPPER(public.departments.name) as department'),
			            DB::raw('academic.grades.grade as grade'),
			            DB::raw('public.departments.id as department_id'),
			            DB::raw('UPPER(academic.classes.class) as class_name'),
			            DB::raw('UPPER(academic.admissions.name) as admission_name'),
			            DB::raw('UPPER(academic.prospect_student_groups.group) as admission_group'),
			        )
			        ->leftJoin('academic.classes','academic.classes.id','=','finance.payment_major_students_view.class_id')
			        ->leftJoin('academic.grades','academic.grades.id','=','academic.classes.grade_id')
			        ->leftJoin('academic.prospect_student_groups','academic.prospect_student_groups.id','=','finance.payment_major_students_view.group_id')
			        ->leftJoin('academic.admissions','academic.admissions.id','=','academic.prospect_student_groups.admission_id')
			        ->join('public.departments','public.departments.id','=','finance.payment_major_students_view.department_id');
        // filter
        $query = $query->where('is_prospect', $request->is_prospect);
		if ($request->is_prospect == 1)
		{
			$query = $query->join('academic.prospect_students','academic.prospect_students.id','=','finance.payment_major_students_view.student_id')
						->where('academic.prospect_students.is_active',1);
		} else {
			$query = $query->join('academic.students','academic.students.id','=','finance.payment_major_students_view.student_id')
						->where('academic.students.is_active',1);
		}
        if (auth()->user()->getDepartment->is_all != 1)
        {
            $query = $query->where('public.departments.id', auth()->user()->department_id);
        }
        if (isset($request->department_id))
        {
            $department_id = $request->department_id;
        } elseif (isset($request->params['fdept'])) {
            $department_id = $request->params['fdept'];
        } else {
            $department_id = '';
        }
        if ($department_id != '') 
        {
            $query = $query->where('public.departments.id', $department_id);
        }
        $class = isset($request->params['fclass']) ? $request->params['fclass'] : '';
        if ($class != '') 
        {
            if ($request->is_prospect == 0)
            {
                $query = $query->where('finance.payment_major_students_view.class_id', $class);
            } else {
                $query = $query->where('finance.payment_major_students_view.group_id', $class);
            }
        }
        $nis = isset($request->params['fnis']) ? $request->params['fnis'] : '';
        if ($nis != '') 
        {
            $query = $query->whereRaw('finance.payment_major_students_view.student_no like ?', ['%'.$nis.'%']);
        }
        $name = isset($request->params['fname']) ? $request->params['fname'] : '';
        if ($name != '') 
        {
            $query = $query->whereRaw('LOWER(finance.payment_major_students_view.name) like ?', ['%'.strtolower($name).'%']);
        }
        // result
        $result["total"] = $query->count();
        $result["rows"] = $query->skip(($param['page'] - 1) * $param['rows'])->take($param['rows'])->orderBy($param['sort'], $param['sort_by'])->get();
        return $result;
    }

	public function dataPaymentStudent(Request $request)
	{
		$query = PaymentMajor::where('bookyear_id',$request['bookyear_id'])
					->whereHas('getReceiptMajor', function($qry) use($request){
						$qry->whereRaw('trans_date::date >= ?', $this->formatDate($request['start_date'],'sys'))
							->whereRaw('trans_date::date <= ?', $this->formatDate($request['end_date'],'sys'));
					});
		$query = $request['is_prospect'] == 0 ? $query->where('student_id',$request['student_id']) : $query->where('prospect_student_id',$request['student_id']);
		return $query->get()->map(function($model){
			$model['period'] = $this->getPeriodName($model->period_month.$model->period_year);
			return $model;
		});
	}    

	public function dataRecapStudent($bookyear_id, $department_id, $grade_id, $class_id)
	{
		return PaymentMajor::select(
					'finance.payment_majors.student_id',
					'academic.students.student_no',
					'academic.students.name',
					'academic.grades.grade',
					'academic.classes.class',
					'academic.students.father',
					'academic.students.mother',
					'academic.students.address',
					'academic.students.postal_code',
					'academic.students.phone',
					'academic.students.father_mobile',
					'academic.students.mother_mobile',
				)
				->join('academic.students','academic.students.id','=','finance.payment_majors.student_id')
				->join('academic.classes','academic.classes.id','=','academic.students.class_id')
				->join('academic.grades','academic.grades.id','=','academic.classes.grade_id')
				->join('academic.schoolyears','academic.schoolyears.id','=','academic.classes.schoolyear_id')
				->where('finance.payment_majors.amount','<>',0)
				->where('finance.payment_majors.is_paid',0)
				->where('finance.payment_majors.is_prospect',0)
				->where('finance.payment_majors.department_id',$department_id)
				->where('finance.payment_majors.bookyear_id',$bookyear_id)
				->where('academic.grades.id',$grade_id)
				->where('academic.students.class_id',$class_id)
				->where('academic.students.is_active',1)
				->get();
	}

	public function recapStudentArrear($bookyear_id, $receipt_id, $student_id)
	{
		return PaymentMajor::select(
					'finance.payment_majors.student_id',
					'finance.payment_majors.amount',
					DB::raw('SUM(finance.receipt_majors.total) as total'),
					'finance.payment_majors.instalment',
					DB::raw('SUM(finance.receipt_majors.discount_amount) as total_discount'),
				)
				->join('finance.receipt_majors','finance.receipt_majors.major_id','=','finance.payment_majors.id')
				->where('finance.payment_majors.receipt_id',$receipt_id)
				->where('finance.payment_majors.student_id',$student_id)
				->where('finance.payment_majors.bookyear_id',$bookyear_id)
				->groupBy('finance.payment_majors.student_id','finance.payment_majors.amount','finance.payment_majors.instalment')
				->get();
	}

	public function recapStudentArrearLast($bookyear_id, $receipt_id, $student_id)
	{
		return PaymentMajor::select(
					'finance.receipt_majors.trans_date',
					'finance.receipt_majors.total',
					'finance.receipt_majors.remark',
				)
				->join('finance.receipt_majors','finance.receipt_majors.major_id','=','finance.payment_majors.id')
				->where('finance.payment_majors.receipt_id',$receipt_id)
				->where('finance.payment_majors.student_id',$student_id)
				->where('finance.payment_majors.bookyear_id',$bookyear_id)
				->orderByDesc('finance.receipt_majors.trans_date')
				->first();
	}

	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'department_id' => $request->department_id, 
				'category_id' => $request->category_id, 
				'prospect_student_id' => $request->prospect_student_id, 
				'student_id' => $request->student_id, 
				'receipt_id' => $request->receipt_id, 
				'amount' => $request->amount, 
				'instalment' => $request->instalment, 
				'journal_id' => $request->journal_id, 
                'bookyear_id' => $request->bookyear_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = PaymentMajor::find($model_id);
			$before = array(
				'department_id' => $query->department_id, 
				'category_id' => $query->category_id, 
				'prospect_student_id' => $query->prospect_student_id, 
				'student_id' => $query->student_id, 
				'receipt_id' => $query->receipt_id, 
				'amount' => $query->amount, 
				'instalment' => $query->instalment, 
				'journal_id' => $query->journal_id, 
                'bookyear_id' => $query->bookyear_id, 
			);
			$after = array(
				'department_id' => $request->has('department_id') ? $request->department_id : $query->department_id, 
				'category_id' => $request->has('category_id') ? $request->category_id : $query->category_id, 
				'prospect_student_id' => $request->has('prospect_student_id') ? $request->prospect_student_id : $query->prospect_student_id, 
				'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id, 
				'receipt_id' => $request->has('receipt_id') ? $request->receipt_id : $query->receipt_id, 
				'amount' => $request->has('amount') ? $request->amount : $query->amount, 
				'instalment' => $request->has('instalment') ? $request->instalment : $query->instalment, 
				'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id, 
                'bookyear_id' => $request->has('bookyear_id') ? $request->bookyear_id : $query->bookyear_id, 
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