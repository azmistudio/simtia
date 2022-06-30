@php
    $GridHeight = rtrim($InnerHeight,'px') - 10 . 'px';
@endphp
<table id="tb-report-prospect-student-arrear" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
    toolbar:'#menubarProspectStudentArrear',method:'post',rownumbers:'true',showFooter:'true'">
    <thead>
        <tr>
            <th data-options="field:'registration_no',width:90,resizeable:true,align:'center'">No.Reg.</th>
            <th data-options="field:'student',width:160,resizeable:true">Nama</th>
            <th data-options="field:'group_name',width:100,align:'center'">Kelompok</th>
            <th data-options="field:'pays',width:300,resizeable:true,align:'center'">Pembayaran</th>
            <th data-options="field:'delayed',width:80,align:'center'">Telat<br/>(hari)</th>
            <th data-options="field:'payment',width:120,resizeable:true,align:'right'">{{ $requests['payment_name'] }}</th>
            <th data-options="field:'major',width:100,resizeable:true,align:'right'">Total<br/>Pembayaran</th>
            <th data-options="field:'discount',width:100,resizeable:true,align:'right'">Total<br/>Diskon</th>
            <th data-options="field:'arrears',width:120,resizeable:true,align:'right'">Total<br/>Tunggakan</th>
            <th data-options="field:'remark',width:200,resizeable:true">Keterangan</th>
        </tr>
    </thead>
</table>
{{-- toolbar --}}
<div id="menubarProspectStudentArrear">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportReportProspectStudentArrear('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportReportProspectStudentArrear('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-report-prospect-student-arrear").datagrid({
            url: "{{ url('finance/report/receipt/student/prospect/arrear/data') }}",
            queryParams: { 
                _token: "{{ csrf_token() }}", 
                department_id: {{ $requests['department_id'] }}, 
                prospect_group_id: {{ $requests['prospect_group_id'] }}, 
                status: 0, 
                category: 'CSWJB',
                duration: {{ $requests['duration'] }},
                date_delay: "{{ $requests['date_delay'] }}",
                payment: {{ $requests['payment'] }},
            },
        })
    })
    function exportReportProspectStudentArrear(document) {
        var dg = $("#tb-report-prospect-student-arrear").datagrid('getData')
        if (dg.total > 0) {
           var payload = {
                department_id: {{ $requests['department_id'] }}, 
                department: "{{ $requests['department'] }}",
                admission: "{{ $requests['admission'] }}",
                prospect_group: "{{ $requests['prospect_group'] }}",
                payment: "{{ $requests['payment_name'] }}",
                payment_id: {{ $requests['payment'] }},
                category: 'CSWJB',
                status: 0,
                duration: {{ $requests['duration'] }},
                date_delay: "{{ $requests['date_delay'] }}",
            }
            exportDocument("{{ url('finance/report/receipt/student/prospect/arrear/export-') }}" + document,payload,"Ekspor data Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>