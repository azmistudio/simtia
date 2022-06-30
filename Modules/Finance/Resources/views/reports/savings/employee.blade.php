@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
    $PanelHeight = $InnerHeight - 271 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:200px;">Pegawai</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-saving-employee">
                <input type="hidden" id="report-saving-employee-no" />
                <select id="AccountingReportEmployee" class="easyui-combogrid cgrd" style="width:200px;height:22px;" data-options="
                    panelWidth: 480,
                    idField: 'id',
                    textField: 'name',
                    fitColumns: true,
                    method: 'post',
                    pagination: true,
                    pageSize:50,
                    pageList:[10,25,50,75,100],
                    columns: [[
                        {field:'employee_id',title:'NIP',width:80,align:'center',sortable:'true'},
                        {field:'name',title:'Nama',width:250},
                        {field:'section',title:'Bagian',width:130},
                    ]],
                "></select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportSavingEmployee(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-saving-employee').form('reset');filterAccountingReportSavingEmployee(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <div id="p-report-saving-employee" class="easyui-panel pnel" style="height:{{ $PanelHeight }};"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportEmployee").combogrid({
            url: "{{ url('hr/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index,row) {
                $("#report-saving-employee-no").val(row.employee_id)
            }
        })
    })
    function filterAccountingReportSavingEmployee(val) {
        if (val > 0) {
            if ($("#AccountingReportEmployee").combogrid("getValue") > 0) {
                $("#p-report-saving-employee").panel("refresh", "{{ url('finance/report/saving/employee/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&employee_id=" + $("#AccountingReportEmployee").combogrid("getValue") 
                    + "&employee=" + $("#AccountingReportEmployee").combogrid("getText") 
                    + "&employee_no=" + $("#report-saving-employee-no").val()
                    + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue") 
                    + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue") 
                )
            }
        } else {
            $("#p-report-saving-employee").panel("clear")
        }
    }
</script>