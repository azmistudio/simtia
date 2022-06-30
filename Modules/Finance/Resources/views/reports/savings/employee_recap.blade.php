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
                <label class="mb-1" style="width:110px;">Dari Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:165px;">Petugas</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-saving-employee-recap">
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <select id="AccountingReportEmployee" class="easyui-combobox cbox" style="width:165px;height:22px;" data-options="panelHeight:125">
                    <option value="0">(Semua Petugas)</option>
                    @foreach ($employees as $employee)
                    <option value="{{ $employee->employee_id }}">{{ $employee->employee }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportSavingEmployeeRecap(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-saving-employee-recap').form('reset');filterAccountingReportSavingEmployeeRecap(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <div id="p-report-saving" class="easyui-panel pnel" style="height:{{ $PanelHeight }};"></div>
            </div>
        </div>
    </div>
</div>
{{-- detail --}}
<div id="report-saving-employee-recap-w" class="easyui-window dwdw" title="Detil Rekapitulasi Penerimaan" data-options="modal:true,closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--View'" style="width:800px;height:500px;padding:10px;"></div>
<script type="text/javascript">
    function filterAccountingReportSavingEmployeeRecap(val) {
        if (val > 0) {
            $("#p-report-saving").panel("refresh", "{{ url('finance/report/saving/employee/recap/view') }}" 
                + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue") 
                + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue") 
                + "&employee_id=" + $("#AccountingReportEmployee").combobox("getValue") 
                + "&employee=" + $("#AccountingReportEmployee").combobox("getText")
            )
        } else {
            $("#p-report-saving").panel("clear")
        }
    }
</script>