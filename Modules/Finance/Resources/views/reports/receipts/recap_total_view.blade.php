@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@inject('receiptOtherEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptOtherEloquent')
@php
    $arr_sub = [];
    $grand_total = 0;
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 pl-2">
            <h6>Data {{ $requests['type'] }} Penerimaan<br/>Petugas: {{ $requests['employee'] }}</h6>
        </div>
        <div class="col-4 p-2 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportReportRecapTotal('pdf')">Ekspor PDF</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportReportRecapTotal('excel')">Ekspor Excel</a>
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
                        if ($requests['receipt_category_id'] == 'JTT' || $requests['receipt_category_id'] == 'CSWJB')
                        {
                            $dataRecapTotal = $receiptMajorEloquent->dataRecapTotal($requests['bookyear_id'], $department->id, $requests['receipt_category_id'], $requests['start_date'], $requests['end_date'], $requests['employee_id']);
                        } elseif ($requests['receipt_category_id'] == 'SKR' || $requests['receipt_category_id'] == 'CSSKR') {
                            $dataRecapTotal = $receiptVoluntaryEloquent->dataRecapTotal($requests['bookyear_id'], $department->id, $requests['receipt_category_id'], $requests['start_date'], $requests['end_date'], $requests['employee_id']);
                        } else {
                            $dataRecapTotal = $receiptOtherEloquent->dataRecapTotal($requests['bookyear_id'], $department->id, $requests['receipt_category_id'], $requests['start_date'], $requests['end_date'], $requests['employee_id']);
                        }
                        $x = 1;
                    @endphp
                    
                    <tr height="25">
                        <td class="text-center" width="5%"><b>No.</b></td>
                        <td class="text-left">&nbsp;<b>Penerimaan</b></td>
                        <td class="text-center" width="20%"><b>Total</b></td>
                    </tr>
                    
                    @foreach ($dataRecapTotal['data'] as $data)
                    @php 
                        $grand_total += $data->total_grand; 
                        if ($department->id == $data->department_id)
                        {
                            $arr_sub[] = array($department->id, $dataRecapTotal['subtotal']);
                        }
                    @endphp

                    <tr height="25">
                        <td class="text-center">{{ $x++ }}</td>
                        <td>&nbsp;{{ $data->receipt_type }}</td>
                        <td class="text-right"><a href="#" onclick="recapTotalDetail('{{ $department->name }}',{{ $department->id }})">Rp{{ number_format($data->total_grand,2) }}</a>&nbsp;</td>
                    </tr>
                    
                    @endforeach
                    
                    <tr height="25">
                        <td colspan="2" class="text-right"><b>Subtotal</b>&nbsp;</td>
                        <td class="text-right">
                            @for ($i = 0; $i < count($arr_sub); $i++)
                                @if ($department->id == $arr_sub[$i][0])
                                <b>Rp{{ number_format($arr_sub[$i][1],2) }}</b>&nbsp;
                                @endif
                            @endfor
                        </td>
                    </tr>
                    
                    @endforeach
                    <tr height="25" bgcolor="#CCFFFF">
                        <td colspan="2" class="text-center"><b>TOTAL PENERIMAAN</b></td>
                        <td class="text-right"><b>Rp{{ number_format($grand_total,2) }}</b>&nbsp;</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    function exportReportRecapTotal(document) {
        var payload = {
            type_id: "{{ $requests['type_id'] }}",
            type: "{{ $requests['type'] }}",
            bookyear_id: {{ $requests['bookyear_id'] }},
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
    function recapTotalDetail(department, department_id) {
        $("#report-receipt-recap-w").window("open").window('refresh', "{{ url('finance/report/receipt/recap/detail') }}" 
                + "?department=" + department
                + "&department_id=" + department_id
                + "&bookyear_id=" + "{{ $requests['bookyear_id'] }}"
                + "&bookyear=" + "{{ $requests['bookyear'] }}"
                + "&type_id=" + "{{ $requests['type_id'] }}"
                + "&type=" + "{{ $requests['type'] }}"
                + "&employee=" + "{{ $requests['employee'] }}"
                + "&employee_id=" + {{ $requests['employee_id'] }}
                + "&receipt_category=" + "{{ $requests['receipt_category'] }}"
                + "&receipt_category_id=" + "{{ $requests['receipt_category_id'] }}"
                + "&start_date=" + "{{ $requests['start_date'] }}"
                + "&end_date=" + "{{ $requests['end_date'] }}"
        )
    }
</script>