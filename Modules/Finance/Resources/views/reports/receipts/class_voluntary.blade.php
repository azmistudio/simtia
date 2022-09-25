@php
    $GridHeight = rtrim($InnerHeight,'px') - 10 . 'px';
@endphp
<table id="tb-report-payment-class" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
    toolbar:'#menubarPaymentClass',method:'post',rownumbers:'true',showFooter:'true'">
    <thead>
        <tr>
            <th data-options="field:'student_no',width:90,resizeable:true,align:'center'">NIS</th>
            <th data-options="field:'student',width:160,resizeable:true">Nama</th>
            <th data-options="field:'class',width:100,align:'center'">Kelas</th>
            <th data-options="field:'pays',width:550,resizeable:true,align:'center'">Pembayaran</th>
            <th data-options="field:'total',width:100,resizeable:true,align:'right'">Total<br/>Pembayaran</th>
        </tr>
    </thead>
</table>
{{-- toolbar --}}
<div id="menubarPaymentClass">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportReportPaymentClass('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportReportPaymentClass('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-report-payment-class").datagrid({
            url: "{{ url('finance/report/receipt/class/data') }}",
            queryParams: { 
                _token: "{{ csrf_token() }}", 
                bookyear_id: {{ $requests['bookyear_id'] }}, 
                class_id: {{ $requests['class_id'] }}, 
                department_id: {{ $requests['department_id'] }}, 
                status: -2, 
                is_prospect: 0,
                period: {{ date('mY') }}
            },
        })
    })
    function exportReportPaymentClass(document) {
        if ($("#AccountingReportPayment").combobox("getValue") > 0) {
           var payload = {
                bookyear_id: {{ $requests['bookyear_id'] }}, 
                class_id: {{ $requests['class_id'] }}, 
                status: -2,
                department_id: {{ $requests['department_id'] }}, 
                department: "{{ $requests['department'] }}",
                grade: "{{ $requests['grade'] }}",
                schoolyear: "{{ $requests['schoolyear'] }}",
                class: "{{ $requests['class'] }}",
                payment: "{{ $requests['payment_name'] }}",
                is_prospect: 0,
                period: {{ date('mY') }}
            }
            exportDocument("{{ url('finance/report/receipt/class/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>