<?php

namespace Modules\Finance\Repositories\Saving;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use App\Http\Traits\AuditLogTrait;
use App\Http\Traits\HelperTrait;
use Modules\Finance\Entities\Saving;
use Modules\Finance\Entities\BookYear;
use Modules\Finance\Entities\JournalDetail;
use Carbon\Carbon;

class SavingEloquent implements SavingRepository
{

	use AuditLogTrait;
	use HelperTrait;

	public function create(Request $request, $subject)
	{
		$payload = $request->all();
		$this->logActivity($request, 0, $subject, 'Tambah');
		return Saving::create($payload);
	}

	public function update(Request $request, $subject)
	{
		$payload = Arr::except($request->all(), ['created_at','_token']);
        $payload['updated_at'] = Carbon::now()->timezone('Asia/Jakarta');
        $this->logActivity($request, $payload['id'], $subject, 'Ubah');
        // 
        return Saving::where('id', $payload['id'])->update($payload);
	}

	public function show($id)
	{
		$t_type = Saving::select('journal_id','transaction_type')->where('id', $id)->first();
        $journal = JournalDetail::select('journal_id','account_id')->where('journal_id', $t_type->journal_id)->orderBy('id')->limit(1);
        return Saving::select(
                    'finance.savings.*',
                    DB::raw('journal_detail.account_id as cash_account'),
                    'finance.saving_types.credit_account',
                    'finance.book_years.book_year',
                    'finance.saving_types.department_id',
                    DB::raw('UPPER(public.departments.name) as department')
                )
                ->join('finance.saving_types','finance.saving_types.id','=','finance.savings.saving_id')
                ->join('public.departments','public.departments.id','=','finance.saving_types.department_id')
                ->join('finance.book_years','finance.book_years.id','=','finance.savings.bookyear_id')
                ->joinSub($journal, 'journal_detail', function($join){
                    $join->on('finance.savings.journal_id','=','journal_detail.journal_id');
                })
                ->where('finance.savings.id', $id)
                ->first();
	}

	public function info(Request $request, $is_employee)
    {
        // total
        $query = Saving::select(
                        DB::raw('SUM(credit - debit) as balance'),
                        DB::raw('SUM(credit) as total_deposit'),
                        DB::raw('SUM(debit) as total_withdraw')
                    )
                    ->where('saving_id', $request->saving_type)
                    ->where('bookyear_id', $request->bookyear_id);
        // last transaction
        $last_deposit = Saving::select('credit','updated_at')->where('saving_id', $request->saving_type)->where('bookyear_id', $request->bookyear_id)->where('credit','>',0); 
        $last_withdraw = Saving::select('debit','updated_at')->where('saving_id', $request->saving_type)->where('bookyear_id', $request->bookyear_id)->where('debit','>',0); 
        if ($is_employee == 0)
        {
            $query = $query->where('student_id', $request->person_id)->first();
            $last_deposit = $last_deposit->where('student_id', $request->person_id);
            $last_withdraw = $last_withdraw->where('student_id', $request->person_id);
        } else {
            $query = $query->where('employee_id', $request->person_id)->first();
            $last_deposit = $last_deposit->where('employee_id', $request->person_id);
            $last_withdraw = $last_withdraw->where('employee_id', $request->person_id);
        }
        $last_deposit = $last_deposit->orderByDesc('id')->first();
        $last_withdraw = $last_withdraw->orderByDesc('id')->first();
        return array(
            'balance' => is_null($query->balance) ? '' : 'Rp'.number_format($query->balance,2),
            'total_deposit' => is_null($query->total_deposit) ? '' : 'Rp'.number_format($query->total_deposit,2),
            'total_withdraw' => is_null($query->total_withdraw) ? '' : 'Rp'.number_format($query->total_withdraw,2),
            'last_deposit' => is_null($last_deposit) ? '' : 'Rp'.number_format($last_deposit->credit,2),
            'last_deposit_date' => is_null($last_deposit) ? '' : ' ('.$this->formatDate($last_deposit->updated_at,'isotime').')',
            'last_withdraw' => is_null($last_withdraw) ? '' : 'Rp'.number_format($last_withdraw->debit,2),
            'last_withdraw_date' => is_null($last_withdraw) ? '' : ' ('.$this->formatDate($last_withdraw->updated_at,'isotime').')',
        );        
    }

	public function data(Request $request, $is_employee)
    {
        $sort = isset($request->sort) ? $request->sort : 'finance.savings.id';
        $order = isset($request->order) ? $request->order : 'asc';
        $person_id = $is_employee == 0 ? $request->student_id : $request->employee_id;
        $query = $this->dataSaving($is_employee, $person_id, $request->bookyear_id, $request->saving_type);
        // result
        $data = array();
        foreach ($query as $val) 
        {
            $data[] = array(
                'id' => $val->id,
                'journal' => '<b>'. $val->cash_no .'</b> / '. $val->journal_date,
                'debit' => number_format($val->debit, 2),
                'credit' => number_format($val->credit, 2),
                'remark' => $val->remark,
                'logged' => $val->employee,
            );            
        }
        $totals = $this->totalSaving($is_employee, $person_id, $request->bookyear_id, $request->saving_type);
        $footer[] = array(
            'journal' => '<b>Total</b>',
            'debit' => '<b>'.number_format($totals->total_debit, 2).'</b>',
            'credit' => '<b>'.number_format($totals->total_credit, 2).'</b>',
        );
        //
        $result["total"] = $query->count();
        $result["rows"] = $data;
        $result["footer"] = $footer;
        return $result;
    }

    public function dataSaving($is_employee, $person_id, $bookyear_id, $saving_type)
    {
        // query
        $query = Saving::select(
                        'finance.savings.id',
                        'finance.journals.cash_no',
                        'finance.journals.journal_date',
                        'finance.journals.bookyear_id',
                        'finance.savings.debit',
                        'finance.savings.credit',
                        'finance.savings.remark',
                        'finance.savings.employee',
                    )
                    ->join('finance.saving_types','finance.saving_types.id','=','finance.savings.saving_id')
                    ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                    ->where('finance.savings.saving_id', $saving_type);
        if ($is_employee == 0)
        {
            $query = $query->where('finance.savings.student_id', $person_id);
        } else {
            $query = $query->where('finance.savings.employee_id', $person_id);
        }
        $query = $query->where('finance.savings.bookyear_id', $bookyear_id)->orderByDesc('finance.savings.id')->get()->map(function($model){
            $model['journal_date'] = $this->formatDate($model['journal_date'],'iso');
            $model['cash_no'] = $this->getPrefixBookYear($model->bookyear_id) . $model->cash_no;
            return $model;
        });
        return $query;
    }

    public function totalSaving($is_employee, $person_id, $bookyear_id, $saving_type)
    {
        $query = Saving::select(
                        DB::raw('SUM(debit) AS total_debit'),
                        DB::raw('SUM(credit) AS total_credit')
                    )
                    ->where('saving_id', $saving_type);
        if ($is_employee == 0)
        {
            $query = $query->where('finance.savings.student_id', $person_id);
        } else {
            $query = $query->where('finance.savings.employee_id', $person_id);
        }
        $query = $query->where('finance.savings.bookyear_id', $bookyear_id)->first();
        return $query;
    }

    public function getBalance($saving_id, $person_id, $is_employee)
    {
        $query = Saving::select(DB::raw('SUM(credit - debit) as value'))->where('saving_id',$saving_id);
        if ($is_employee > 0)
        {
            $query = $query->where('employee_id', $person_id);
        } else {
            $query = $query->where('student_id', $person_id);
        }
        $query = $query->first();
        return is_null($query) ? 0 : $query->value;
    }

    public function dataPayment(Request $request)
    {
        $query = Saving::where('bookyear_id',$request['bookyear_id'])
                    ->whereRaw('trans_date::date >= ?', $this->formatDate($request['start_date'],'sys'))
                    ->whereRaw('trans_date::date <= ?', $this->formatDate($request['end_date'],'sys'));
        $query = $request['is_employee'] == 0 ? $query->where('student_id',$request['student_id']) : $query->where('employee_id',$request['employee_id']);
        return $query->get();
    }

    public function totalPaymentReceipt($receipt_id, $student_id, $is_prospect)
    {
        $query = Saving::select(DB::raw('SUM(total) as total_receipt'))->where('receipt_id',$receipt_id);
        $query = $is_prospect == 0 ? $query->where('student_id',$student_id) : $query->where('prospect_student_id',$student_id);
        return $query->where('is_prospect',$is_prospect)->first();
    }

    public function lastPaymentReceipt($receipt_id, $student_id, $is_prospect)
    {
        $query = Saving::where('receipt_id',$receipt_id);
        $query = $is_prospect == 0 ? $query->where('student_id',$student_id) : $query->where('prospect_student_id',$student_id);
        $query = $query->where('is_prospect',$is_prospect)->orderByDesc('trans_date')->limit(1)->get()->map(function($model){
            $model['trans_date'] = $this->formatDate($model['trans_date'],'iso');
            return $model;
        });
        return $query;
    }

    public function dataSavingClass(Request $request)
    {
        $query = Saving::select(
                    'finance.savings.student_id',
                    'academic.students.student_no',
                    'academic.students.name',
                    'academic.classes.class',
                )
                ->join('finance.saving_types','finance.saving_types.id','=','finance.savings.saving_id')
                ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                ->join('academic.students','academic.students.id','=','finance.savings.student_id')
                ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                ->where('finance.savings.bookyear_id', $request->bookyear_id)
                ->where('finance.savings.saving_id', $request->saving_id)
                ->where('finance.saving_types.department_id', $request->department_id)
                ->where('academic.students.class_id', $request->class_id);
        // result
        $result["total"] = $query->distinct()->count('finance.savings.student_id');
        $result["rows"] = $query->orderBy('finance.savings.student_id')->get()->map(function($model) use ($request){
            $queryBalance = $this->getSavingBalance(0, $model->student_id, $request->bookyear_id, $request->saving_id, $request->department_id, $request->class_id)
                                ->select(
                                    DB::raw('SUM(credit) - SUM(debit) as balance'),
                                    DB::raw('SUM(credit) as total_saving'),
                                    DB::raw('SUM(debit) as total_withdraw'),
                                )
                                ->groupBy('finance.savings.student_id')
                                ->first();
            //                                
            $queryLastDebit = $this->getSavingBalance(0, $model->student_id, $request->bookyear_id, $request->saving_id, $request->department_id, $request->class_id)
                                ->where('finance.savings.transaction_type', 'debit')
                                ->select(
                                    'finance.savings.created_at',
                                    'finance.savings.debit',
                                )
                                ->orderByDesc('finance.savings.created_at')
                                ->first();
            //
            $queryLastCredit = $this->getSavingBalance(0, $model->student_id, $request->bookyear_id, $request->saving_id, $request->department_id, $request->class_id)
                                ->where('finance.savings.transaction_type', 'credit')
                                ->select(
                                    'finance.savings.created_at',
                                    'finance.savings.credit',
                                )
                                ->orderByDesc('finance.savings.created_at')
                                ->first();
            // 
            $debit = !empty($queryLastDebit->debit) ? $queryLastDebit->debit : 0;
            $last_debit = !empty($queryLastDebit->created_at) ? $this->formatDate($queryLastDebit->created_at,'isotime') : '-'; 
            $credit = !empty($queryLastCredit->credit) ? $queryLastCredit->credit : 0;
            $last_credit = !empty($queryLastCredit->created_at) ? $this->formatDate($queryLastCredit->created_at,'isotime') : '-'; 
            // 
            $model['name'] = Str::title($model->name);
            $model['class'] = Str::upper($model->class);
            $model['_balance'] = $queryBalance->balance;
            $model['_total_saving'] = $queryBalance->total_saving;
            $model['_total_withdraw'] = $queryBalance->total_withdraw;
            $model['balance'] = 'Rp'. number_format($queryBalance->balance,2);
            $model['total_saving'] = 'Rp'. number_format($queryBalance->total_saving,2);
            $model['last_saving'] = 'Rp'. number_format($credit,2) .'<br/>'. $last_credit;
            $model['total_withdraw'] = 'Rp'. number_format($queryBalance->total_withdraw,2);
            $model['last_withdraw'] = 'Rp'. number_format($debit,2) .'<br/>'. $last_debit;
            return $model;
        });

        $footer = array([
            'class' => '<b>TOTAL</b>', 
            'balance' => '<b>Rp'.number_format($result['rows']->sum('_balance'),2).'</b>',
            'total_saving' => '<b>Rp'.number_format($result['rows']->sum('_total_saving'),2).'</b>',
            'total_withdraw' => '<b>Rp'.number_format($result['rows']->sum('_total_withdraw'),2).'</b>',
        ]);
        $result["footer"] = $footer;
        return $result;
    }

    public function getSavingBalance($is_employee, $person_id, $bookyear_id, $saving_id, $department_id, $class_id)
    {
        if ($is_employee == 0)
        {
            return Saving::join('finance.saving_types','finance.saving_types.id','=','finance.savings.saving_id')
                    ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                    ->join('academic.students','academic.students.id','=','finance.savings.student_id')
                    ->join('academic.classes','academic.classes.id','=','academic.students.class_id')
                    ->where('finance.savings.bookyear_id', $bookyear_id)
                    ->where('finance.savings.saving_id', $saving_id)
                    ->where('finance.saving_types.department_id', $department_id)
                    ->where('finance.savings.student_id', $person_id)
                    ->where('academic.students.class_id', $class_id);
        }
    }

    public function dataSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date)
    {
        $query = Saving::select('saving_id')
                    ->where('bookyear_id', $bookyear_id)
                    ->whereDate('trans_date','>=',$this->formatDate($start_date,'sys'))
                    ->whereDate('trans_date','<=',$this->formatDate($end_date,'sys'));
        //
        if ($is_employee == 0)
        {
            $query = $query->where('student_id', $person_id);
        } else {
            $query = $query->where('employee_id', $person_id);
        }
        return $query->groupBy('saving_id')
                     ->get()->map(function($model){
                            $model['saving_type'] = $model->getSavingType->name;
                            return $model;
                        });;
    }

    public function dataSavingDetailInfo($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id)
    {
        $queryTotal = $this->getSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id)
                        ->select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
                        ->groupBy('saving_id')
                        ->first();
        // 
        $querySubTotal = $this->getSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id)
                            ->whereDate('trans_date','>=',$this->formatDate($start_date,'sys'))
                            ->whereDate('trans_date','<=',$this->formatDate($end_date,'sys'))
                            ->select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'))
                            ->groupBy('saving_id')
                            ->first();
        //
        $queryLastDebit = $this->getSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id)
                            ->whereDate('trans_date','>=',$this->formatDate($start_date,'sys'))
                            ->whereDate('trans_date','<=',$this->formatDate($end_date,'sys'))
                            ->where('debit','<>',0)
                            ->select('debit','created_at')
                            ->orderByDesc('id')
                            ->first();
        //
        $queryLastCredit = $this->getSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id)
                            ->whereDate('trans_date','>=',$this->formatDate($start_date,'sys'))
                            ->whereDate('trans_date','<=',$this->formatDate($end_date,'sys'))
                            ->where('credit','<>',0)
                            ->select('credit','created_at')
                            ->orderByDesc('id')
                            ->first();

        $debit = !empty($queryLastDebit->debit) ? $queryLastDebit->debit : 0;
        $last_debit = !empty($queryLastDebit->created_at) ? $this->formatDate($queryLastDebit->created_at,'isotime') : '-'; 
        $credit = !empty($queryLastCredit->credit) ? $queryLastCredit->credit : 0;
        $last_credit = !empty($queryLastCredit->created_at) ? $this->formatDate($queryLastCredit->created_at,'isotime') : '-'; 
        // 

        return array(
            'deposit'       => 'Rp'. number_format($querySubTotal->total_credit,2),
            'withdraw'      => 'Rp'. number_format($querySubTotal->total_debit,2),
            'last_deposit'  => '<b>Rp'. number_format($credit,2) .'</b>&nbsp;<br/>'. $last_credit,
            'last_withdraw' => '<b>Rp'. number_format($debit,2) .'</b>&nbsp;<br/>'. $last_debit,
            'total_deposit' => 'Rp'. number_format($queryTotal->total_credit,2),
            'total_withdraw'=> 'Rp'. number_format($queryTotal->total_debit,2),
            'total_balance' => 'Rp'. number_format($queryTotal->total_credit - $queryTotal->total_debit,2),
        );
    }

    private function getSavingDetail($is_employee, $person_id, $bookyear_id, $start_date, $end_date, $saving_id)
    {
        $query = Saving::where('bookyear_id', $bookyear_id)->where('saving_id', $saving_id);
        if ($is_employee == 0)
        {
            $query = $query->where('student_id', $person_id);
        } else {
            $query = $query->where('employee_id', $person_id);
        }
        return $query;
    }

    public function dataSavingRecap($is_employee, $department_id, $employee_id, $start_date, $end_date)
    {
        $query = Saving::select(DB::raw('SUM(debit) as total_debit, SUM(credit) as total_credit'),'finance.savings.saving_id')
                    ->join('finance.saving_types','finance.saving_types.id','=','finance.savings.saving_id')
                    ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                    ->whereDate('trans_date','>=',$this->formatDate($start_date,'sys'))
                    ->whereDate('trans_date','<=',$this->formatDate($end_date,'sys'));
        if ($is_employee == 0)
        {
            $query = $query->where('finance.savings.is_employee', 0)->where('finance.saving_types.department_id', $department_id);
        } else {
            $query = $query->where('finance.savings.is_employee', 1);
        }
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        } 
        return $query->groupBy('finance.savings.saving_id')->get()->map(function($model){
                    $model['saving_type'] = $model->getSavingType->name;
                    return $model;
                });
    }

    public function dataSavingDetailTrx($is_employee, $department_id, $saving_id, $employee_id, $start_date, $end_date, $type)
    {
        $query = Saving::select(
                        'finance.savings.trans_date',
                        'finance.savings.student_id',
                        'finance.savings.employee_id',
                        'finance.savings.debit',
                        'finance.savings.credit',
                        'finance.savings.employee',
                    )
                    ->join('finance.saving_types','finance.saving_types.id','=','finance.savings.saving_id')
                    ->join('finance.journals','finance.journals.id','=','finance.savings.journal_id')
                    ->where('finance.savings.saving_id', $saving_id)
                    ->where('finance.savings.transaction_type', $type)
                    ->whereDate('trans_date','>=',$this->formatDate($start_date,'sys'))
                    ->whereDate('trans_date','<=',$this->formatDate($end_date,'sys'));
        if ($is_employee == 0)
        {
            $query = $query->where('finance.savings.is_employee', 0)->where('finance.saving_types.department_id', $department_id);
        } else {
            $query = $query->where('finance.savings.is_employee', 1);
        }
        if ($employee_id > 0)
        {
            $query = $query->where('finance.journals.employee_id', $employee_id);
        }
        return $query->get()->map(function($model){
                        $model['trans_date'] = $this->formatDate($model->trans_date,'iso');
                        $model['student'] = !empty($model->student_id) ? $model->getStudent->student_no .' - '. $model->getStudent->name : '-';
                        $model['employee_name'] = !empty($model->employee_id) ? $this->getEmployeeName($model->employee_id) : '-';
                        return $model;
                    });
    }
	
	private function logActivity(Request $request, $model_id, $subject, $action) 
	{
		if ($action == 'Tambah')
		{
			$data = array(
				'employee_id' => $request->employee_id, 
				'student_id' => $request->student_id, 
				'is_employee' => $request->is_employee, 
				'saving_id' => $request->saving_id, 
				'journal_id' => $request->journal_id, 
				'trans_date' => $request->trans_date, 
                'debit' => $request->debit, 
                'credit' => $request->credit, 
                'bookyear_id' => $request->bookyear_id, 
			);
			$this->logTransaction('#', $action .' '. $subject, '{}', json_encode($data));
		} else {
			$query = Saving::find($model_id);
			$before = array(
				'employee_id' => $query->employee_id, 
				'student_id' => $query->student_id, 
				'is_employee' => $query->is_employee, 
				'saving_id' => $query->saving_id, 
				'journal_id' => $query->journal_id, 
				'trans_date' => $query->trans_date, 
                'debit' => $query->debit, 
                'credit' => $query->credit, 
                'bookyear_id' => $query->bookyear_id, 
			);
			$after = array(
				'employee_id' => $request->has('employee_id') ? $request->employee_id : $query->employee_id, 
				'student_id' => $request->has('student_id') ? $request->student_id : $query->student_id, 
				'is_employee' => $request->has('is_employee') ? $request->is_employee : $query->is_employee, 
				'saving_id' => $request->has('saving_id') ? $request->saving_id : $query->saving_id, 
				'journal_id' => $request->has('journal_id') ? $request->journal_id : $query->journal_id, 
				'trans_date' => $request->has('trans_date') ? $request->trans_date : $query->trans_date, 
                'debit' => $request->has('debit') ? $request->debit : $query->debit, 
                'credit' => $request->has('credit') ? $request->credit : $query->credit, 
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