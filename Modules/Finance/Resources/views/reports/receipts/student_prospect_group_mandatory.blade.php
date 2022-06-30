@php
    $GridHeight = rtrim($InnerHeight,'px') - 10 . 'px';
@endphp
<table id="tb-report-payment-prospect-group" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
    toolbar:'#menubarPaymentProspectGroup',method:'post',rownumbers:'true',showFooter:'true'">
    <thead>
        <tr>
            <th data-options="field:'student_no',width:90,resizeable:true,align:'center'">No.Reg.</th>
            <th data-options="field:'student',width:160,resizeable:true">Nama</th>
            <th data-options="field:'group',width:100,align:'center'">Kelompok</th>
            <th data-options="field:'pays',width:300,resizeable:true,align:'center'">Pembayaran</th>
            <th data-options="field:'status',width:90">Status</th>
            <th data-options="field:'payment',width:120,resizeable:true,align:'right'">{{ $requests['payment_name'] }}</th>
            <th data-options="field:'major',width:100,resizeable:true,align:'right'">Besar<br/>Pembayaran</th>
            <th data-options="field:'discount',width:100,resizeable:true,align:'right'">Total<br/>Diskon</th>
            <th data-options="field:'arrears',width:120,resizeable:true,align:'right'">Total<br/>Tunggakan</th>
            <th data-options="field:'remark',width:200,resizeable:true">Keterangan</th>
        </tr>
    </thead>
</table>
{{-- toolbar --}}
<div id="menubarPaymentProspectGroup">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportReportPaymentProspectGroup('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportReportPaymentProspectGroup('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-report-payment-prospect-group").datagrid({
            url: "{{ url('finance/report/receipt/student/prospect/group/data') }}",
            queryParams: { 
                _token: "{{ csrf_token() }}", 
                department_id: {{ $requests['department_id'] }}, 
                prospect_group_id: {{ $requests['prospect_group_id'] }}, 
                status: {{ $requests['status'] }}, 
                category: 'CSWJB'
            },
        })
    })
    function exportReportPaymentProspectGroup(document) {
        if ($("#AccountingReportPayment").combobox("getValue") > 0) {
           var payload = {
                prospect_group_id: {{ $requests['prospect_group_id'] }}, 
                status: {{ $requests['status'] }},
                department_id: {{ $requests['department_id'] }}, 
                department: "{{ $requests['department'] }}",
                prospect_group: "{{ $requests['prospect_group'] }}",
                payment: "{{ $requests['payment_name'] }}",
                is_prospect: 1,
                category: 'CSWJB'
            }
            exportDocument("{{ url('finance/report/receipt/student/prospect/group/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>