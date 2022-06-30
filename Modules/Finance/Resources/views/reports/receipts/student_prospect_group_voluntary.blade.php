@php
    $GridHeight = rtrim($InnerHeight,'px') - 10 . 'px';
@endphp
<table id="tb-report-prospect-group" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
    toolbar:'#menubarProspectGroup',method:'post',rownumbers:'true',showFooter:'true'">
    <thead>
        <tr>
            <th data-options="field:'student_no',width:90,resizeable:true,align:'center'">No. Registrasi</th>
            <th data-options="field:'student',width:160,resizeable:true">Nama</th>
            <th data-options="field:'group',width:100,align:'center'">Kelompok</th>
            <th data-options="field:'pays',width:550,resizeable:true,align:'center'">Pembayaran</th>
            <th data-options="field:'total',width:100,resizeable:true,align:'right'">Total<br/>Pembayaran</th>
        </tr>
    </thead>
</table>
{{-- toolbar --}}
<div id="menubarProspectGroup">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportReportProspectGroup('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportReportProspectGroup('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-report-prospect-group").datagrid({
            url: "{{ url('finance/report/receipt/student/prospect/group/data') }}",
            queryParams: { 
                _token: "{{ csrf_token() }}", 
                department_id: {{ $requests['department_id'] }}, 
                prospect_group_id: {{ $requests['prospect_group_id'] }}, 
                status: -2, 
                category: 'CSSKR'
            },
        })
    })
    function exportReportProspectGroup(document) {
        if ($("#AccountingReportPayment").combobox("getValue") > 0) {
           var payload = {
                prospect_group_id: {{ $requests['prospect_group_id'] }}, 
                status: -2,
                department_id: {{ $requests['department_id'] }}, 
                department: "{{ $requests['department'] }}",
                prospect_group: "{{ $requests['prospect_group'] }}",
                payment: "{{ $requests['payment_name'] }}",
                is_prospect: 1,
                category: 'CSSKR'
            }
            exportDocument("{{ url('finance/report/receipt/student/prospect/group/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>