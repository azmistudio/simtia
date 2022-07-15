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
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">No. Jurnal</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-transaction">
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
                <input id="AccountingReportCashNo" class="easyui-textbox tbox" style="width:150px;height:22px;" data-options="" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportTransaction()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-transaction').form('reset');filterAccountingReportTransaction()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-accounting-report-transaction" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,showFooter:true,pagination:true,toolbar:'#toolbarAccountingReportTransaction',pageSize:50,pageList:[10,25,50,75,100]">
                    <thead>
                        <tr>
                            <th data-options="field:'department_name',width:120,sortable:true,align:'center'">Departemen</th>
                            <th data-options="field:'journal',width:160,sortable:true,align:'center'">No. Jurnal/Tgl.</th>
                            <th data-options="field:'employee',width:150,resizeable:true,align:'center'">Petugas</th>
                            <th data-options="field:'transaction',width:275,resizeable:true">Transaksi</th>
                            <th data-options="field:'debit',width:120,align:'right'">Debit</th>
                            <th data-options="field:'credit',width:120,align:'right'">Kredit</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAccountingReportTransaction">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Transaksi Keuangan</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAccountingReportTransaction('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAccountingReportTransaction('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-accounting-report-transaction").datagrid()
    })
    function filterAccountingReportTransaction() {
        $("#tb-accounting-report-transaction").datagrid("reload", "{{ url('finance/report/transaction/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&bookyear_id=" + $("#AccountingReportBookYear").combobox("getValue")
            + "&department_id=" + $("#AccountingReportDept").combobox("getValue")  
            + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue")
            + "&cash_no=" + $("#AccountingReportCashNo").textbox("getValue")
        )
    }
    function exportAccountingReportTransaction(document) {
        var dg = $("#tb-accounting-report-transaction").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AccountingReportDept").combobox("getText"),
                bookyear: $("#AccountingReportBookYear").textbox("getText"),
                start: $("#AccountingReportDateFrom").datebox("getValue"), 
                end: $("#AccountingReportDateTo").datebox("getValue"),
                rows: dg.rows, 
                footer: $("#tb-accounting-report-transaction").datagrid("getFooterRows")
            }
            exportDocument("{{ url('finance/report/transaction/export-') }}" + document,payload,"Ekspor Transaksi Keuangan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>