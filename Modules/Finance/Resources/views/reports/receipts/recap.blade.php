@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
    $PanelHeight = $InnerHeight - 325 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:100px;">Tahun Buku:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:145px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Jenis:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:155px;">Laporan</label>
            </div>
            <form id="form-accounting-report-receipt-recap">
            <div class="col-12">
                <select id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:100px;height:22px;" data-options="panelHeight:68">
                    @foreach ($bookyears as $bookyear)
                    <option value="{{ $bookyear->id }}">{{ $bookyear->is_active == 1 ? $bookyear->book_year . ' (A)' : $bookyear->book_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportDept" class="easyui-combobox cbox" style="width:145px;height:22px;" data-options="panelHeight:125">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <option value="{{ auth()->user()->department_id }}">{{ auth()->user()->getDepartment->name }}</option>
                    @else 
                        <option value="1">(Semua Dept.)</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    @endif
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportReceiptCategory" class="easyui-combobox cbox" style="width:200px;height:22px;" data-options="panelHeight:125">
                    @foreach ($categories as $category)
                    <option value="{{ $category->code }}">{{ $category->category }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <select id="AccountingReportType" class="easyui-combobox cbox" style="width:155px;height:22px;" data-options="panelHeight:46">
                    <option value="total">Rekapitulasi Total</option>
                    <option value="daily">Rekapitulasi Harian</option>
                </select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportReceiptRecap(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-receipt-recap').form('reset');filterAccountingReportReceiptRecap(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
            </div>
            <div class="col-12 mt-1">
                <label class="mb-1" style="width:257px;">Petugas</label>
            </div>
            <div class="col-12">
                <select id="AccountingReportEmployee" class="easyui-combobox cbox" style="width:257px;height:22px;" data-options="panelHeight:125">
                    <option value="0">(Semua Petugas)</option>
                    @foreach ($employees as $employee)
                    <option value="{{ $employee->employee_id }}">{{ $employee->employee }}</option>
                    @endforeach
                </select>
            </div>
            </form>
            <div class="col-12 mt-2">
                <div id="p-report-receipt" class="easyui-panel pnel" style="height:{{ $PanelHeight }};"></div>
            </div>
        </div>
    </div>
</div>
{{-- detail --}}
<div id="report-receipt-recap-w" class="easyui-window dwdw" title="Detil Rekapitulasi Penerimaan" data-options="modal:true,closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--View'" style="width:800px;height:500px;padding:10px;"></div>
<script type="text/javascript">
    function filterAccountingReportReceiptRecap(val) {
        if (val > 0) {
            $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/recap/view') }}" 
                + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                + "&bookyear_id=" + $("#AccountingReportBookYear").combobox("getValue") 
                + "&bookyear=" + $("#AccountingReportBookYear").combobox("getText") 
                + "&department_id=" + $("#AccountingReportDept").combobox("getValue") 
                + "&department=" + $("#AccountingReportDept").combobox("getText") 
                + "&receipt_category_id=" + $("#AccountingReportReceiptCategory").combobox("getValue") 
                + "&receipt_category=" + $("#AccountingReportReceiptCategory").combobox("getText") 
                + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue") 
                + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue") 
                + "&type_id=" + $("#AccountingReportType").combobox("getValue") 
                + "&type=" + $("#AccountingReportType").combobox("getText") 
                + "&employee_id=" + $("#AccountingReportEmployee").combobox("getValue") 
                + "&employee=" + $("#AccountingReportEmployee").combobox("getText")
            )
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>