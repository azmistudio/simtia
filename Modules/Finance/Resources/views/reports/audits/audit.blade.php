@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:100px;">Tahun Buku:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-audit">
                <select name="bookyear_id" id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:100px;height:22px;" data-options="panelHeight:68">
                    @foreach ($bookyears as $bookyear)
                    <option value="{{ $bookyear->id }}">{{ $bookyear->is_active == 1 ? $bookyear->book_year . ' (A)' : $bookyear->book_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <select name="department_id" id="AccountingReportDept" class="easyui-combobox cbox" style="width:200px;height:22px;" data-options="panelHeight:125">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <option value="{{ auth()->user()->department_id }}">{{ auth()->user()->getDepartment->name }}</option>
                    @else 
                        <option value="1">SEMUA DEPARTEMEN</option>
                        @foreach ($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    @endif
                </select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportAudit()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-audit').form('reset');filterAccountingReportAudit(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-accounting-report-audit" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,pagination:false,singleSelect:true">
                    <thead>
                        <tr>
                            <th data-options="field:'source',width:100,hidden:true">Sumber</th>
                            <th data-options="field:'changed',width:350,resizeable:true">Perubahan</th>
                            <th data-options="field:'total',width:120,align:'center'">Jumlah</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="dlg-accounting-audit" class="easyui-window dwdw" title="Data Audit" style="width:1200px;height:550px;padding:10px" data-options="modal: true, closed:true, minimizable:false, maximizable:false, iconCls: 'ms-Icon ms-Icon--Database'"></div>
<script type="text/javascript">
    $(function () {
        $("#tb-accounting-report-audit").datagrid({
            onDblClickRow: function(index, row) {
                $("#dlg-accounting-audit").window("open")
                $("#dlg-accounting-audit").window("refresh", "{{ url('finance/report/audit/view') }}" + "?source=" + row.source + "&bookyear_id=" + $("#AccountingReportBookYear").combobox("getValue") + "&department=" + $("#AccountingReportDept").combobox("getText") + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue") + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue") + "&bookyear=" + $("#AccountingReportBookYear").textbox("getText") + "&department_id=" + $("#AccountingReportDept").combobox("getValue"))
            }
        })
    })
    function filterAccountingReportAudit() {
        $("#tb-accounting-report-audit").datagrid("reload", "{{ url('finance/report/audit/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&bookyear_id=" + $("#AccountingReportBookYear").combobox("getValue")
            + "&department_id=" + $("#AccountingReportDept").combobox("getValue")
            + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue")
        )
    }
</script>