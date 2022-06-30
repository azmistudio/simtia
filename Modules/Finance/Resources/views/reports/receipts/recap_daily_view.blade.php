@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptTypeEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptTypeEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@inject('receiptOtherEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent')
@php
	$subtotal = 0;
	$total_type = 0;
	$count_name = 0;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2">
            <h6>Data {{ $requests['type'] }} Penerimaan<br/>Petugas: {{ $requests['employee'] }}</h6>
        </div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReportRecapDaily('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportRecapDaily('excel')">Ekspor Excel</a>
        </div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-2">
            <table border="1" style="width:100%;border-collapse:collapse">
                <tbody>
      				{{-- department --}}
                    @foreach ($departments as $department)
                    
                    <tr height="35">
                        <td colspan="4" bgcolor="#CCCFFF">&nbsp;<b>Departemen: {{ $department->name }}</b></td>
                    </tr>

                    @php
                        $x = 1;
                        if ($requests['receipt_category_id'] == 'JTT' || $requests['receipt_category_id'] == 'CSWJB')
                        {
                            $dataRecapDaily = $receiptMajorEloquent->dataRecapDaily($requests['bookyear_id'], $department->id, $requests['receipt_category_id'], $requests['start_date'], $requests['end_date'], $requests['employee_id']);
                        } elseif ($requests['receipt_category_id'] == 'SKR' || $requests['receipt_category_id'] == 'CSSKR') {
                            $dataRecapDaily = $receiptVoluntaryEloquent->dataRecapDaily($requests['bookyear_id'], $department->id, $requests['receipt_category_id'], $requests['start_date'], $requests['end_date'], $requests['employee_id']);
                        } else {
                            $dataRecapDaily = $receiptOtherEloquent->dataRecapDaily($requests['bookyear_id'], $department->id, $requests['receipt_category_id'], $requests['start_date'], $requests['end_date'], $requests['employee_id']);
                        }
                    	$receiptTypeNames = $receiptTypeEloquent->search($requests['receipt_category_id'], $department->id);
                    	$count_name = count($receiptTypeNames);
                    @endphp

                    <tr height="25">
                        <td class="text-center" width="5%"><b>No.</b></td>
                        <td class="text-center" width="10%"><b>Tanggal</b></td>
                        @foreach ($receiptTypeNames as $type)
                        <td class="text-center"><b>{{ $type->name }}</b></td>
                        @endforeach
                        <td class="text-center" width="15%"><b>Sub Total</b></td>
                    </tr>

                    @foreach ($dataRecapDaily as $data)

                    <tr height="25">
                        <td class="text-center">{{ $x++ }}</td>
                        <td class="text-center">&nbsp;{{ $data->trans_date }}</td>
                        @foreach ($receiptTypeNames as $type)
                        @php
                            if ($requests['receipt_category_id'] == 'JTT' || $requests['receipt_category_id'] == 'CSWJB')
                            {
                                $recapTransaction = $receiptMajorEloquent->dataRecapDailyTrans($requests['bookyear_id'], $department->id, $type->id, $data->trans_date, $requests['employee_id']);
                            } elseif ($requests['receipt_category_id'] == 'SKR' || $requests['receipt_category_id'] == 'CSSKR') {
                                $recapTransaction = $receiptVoluntaryEloquent->dataRecapDailyTrans($requests['bookyear_id'], $department->id, $type->id, $data->trans_date, $requests['employee_id']);
                            } else {
                                $recapTransaction = $receiptOtherEloquent->dataRecapDailyTrans($requests['bookyear_id'], $department->id, $type->id, $data->trans_date, $requests['employee_id']);
                            }   
                            $total_type += $recapTransaction['transaction']->total;
                        	$subtotal += $recapTransaction['subtotal']->total;
                        @endphp
                        <td class="text-right">
                        	<a href="#" onclick="recapTotalDetail('{{ $department->name }}',{{ $department->id }},'{{ $data->trans_date }}','{{ $type->name }}',{{ $type->id }})">
                        		Rp{{ number_format($recapTransaction['transaction']->total,2) }}
                        	</a>&nbsp;
                        </td>
                        <td class="text-right">Rp{{ number_format($recapTransaction['subtotal']->total,2) }}&nbsp;</td>
                        @endforeach
                    </tr>

                    @endforeach

                    @endforeach
                    <tr height="25" bgcolor="#CCFFFF">
                        <td colspan="2" class="text-center"><b>TOTAL PENERIMAAN</b></td>
                        @for ($i = 0; $i < $count_name; $i++)
                        <td class="text-right">Rp{{ number_format($total_type,2) }}</td>
                        @endfor
                        <td class="text-right">Rp{{ number_format($subtotal,2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function exportReportRecapDaily(document) {
        var payload = {
            type_id: "{{ $requests['type_id'] }}",
            type: "{{ $requests['type'] }}",
            bookyear_id: "{{ $requests['bookyear_id'] }}",
            bookyear: "{{ $requests['bookyear'] }}",
            department_id: {{ $requests['department_id'] }},
            department: "{{ $requests['department'] }}",
            receipt_category_id: "{{ $requests['receipt_category_id'] }}",
            receipt_category: "{{ $requests['receipt_category'] }}",
            employee_id: {{ $requests['employee_id'] }},
            employee: "{{ $requests['employee'] }}",
            start_date: "{{ $requests['start_date'] }}",
            end_date: "{{ $requests['end_date'] }}",
        }
        exportDocument("{{ url('finance/report/receipt/recap/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
    }
    function recapTotalDetail(department, department_id, trans_date, type_name, type_id) {
        $("#report-receipt-recap-w").window("open").window('refresh', "{{ url('finance/report/receipt/recap/detail') }}" 
                + "?department=" + department
                + "&department_id=" + department_id
                + "&type_id=" + type_id
                + "&type=" + type_name
                + "&bookyear=" + "{{ $requests['bookyear'] }}"
                + "&bookyear_id=" + {{ $requests['bookyear_id'] }}
                + "&employee=" + "{{ $requests['employee'] }}"
                + "&employee_id=" + {{ $requests['employee_id'] }}
                + "&receipt_category=" + "{{ $requests['receipt_category'] }}"
                + "&receipt_category_id=" + "{{ $requests['receipt_category_id'] }}"
                + "&trans_date=" + trans_date
        )
    }
</script>