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
                <label class="mb-1" style="width:100px;">Tahun Buku:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:145px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Penerimaan:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-receipt-other">
                <input type="hidden" id="id-department-receipt-other" value="-1" />
                <select id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:100px;height:22px;" data-options="panelHeight:68">
                    @foreach ($bookyears as $bookyear)
                    <option value="{{ $bookyear->id }}">{{ $bookyear->is_active == 1 ? $bookyear->book_year . ' (A)' : $bookyear->book_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:145px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportReceiptType" class="easyui-combogrid cgrd" style="width:200px;height:22px;" data-options="
                    panelWidth: 350,
                    idField: 'id',
                    textField: 'name',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:150},
                        {field:'name',title:'Penerimaan',width:200}
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportReceiptOther(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-receipt-other').form('reset');filterAccountingReportReceiptOther(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <table id="tb-accounting-report-receipt-other" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
                    toolbar:'#menubarReceiptOther',method:'post',rownumbers:'true',showFooter:'true'">
                    <thead>
                        <tr>
                            <th data-options="field:'journal',width:100,resizeable:true,align:'center'">Jurnal</th>
                            <th data-options="field:'source',width:160,resizeable:true">Sumber</th>
                            <th data-options="field:'total',width:100,align:'right'">Jumlah</th>
                            <th data-options="field:'remark',width:200,resizeable:true">Keterangan</th>
                            <th data-options="field:'employee',width:200,resizeable:true">Petugas</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="menubarReceiptOther">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAccountintReportReceiptOther('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAccountintReportReceiptOther('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportReceiptType").combogrid('grid').datagrid({
            url: '{{ url('finance/receipt/type/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', category_id: {{ $category_id }} },
            onClickRow: function(index, row) {
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#id-department-receipt-other").val(row.department_id)
                $("#AccountingReportReceiptType").combogrid('hidePanel')
            }
        })
        $("#tb-accounting-report-receipt-other").datagrid()
    })
    function filterAccountingReportReceiptOther(val) {
        if (val > 0) {
            $("#tb-accounting-report-receipt-other").datagrid("reload", "{{ url('finance/report/receipt/other/data') }}" 
                + "?_token=" + "{{ csrf_token() }}" 
                + "&department_id=" + $("#id-department-receipt-other").val()
                + "&bookyear_id=" + $("#AccountingReportBookYear").combogrid("getValue")
                + "&receipt_id=" + $("#AccountingReportReceiptType").combogrid("getValue")
                + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue")
                + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue")
            )
        } 
    }
    function exportAccountintReportReceiptOther(document) {
        var dg = $("#tb-accounting-report-receipt-other").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AccountingReportDept").combobox("getText"),
                receipt: $("#AccountingReportBookYear").combogrid("getText"),
                receipt_id: $("#AccountingReportBookYear").combogrid("getValue"),
                receipt: $("#AccountingReportReceiptType").combogrid("getText"),
                receipt_id: $("#AccountingReportReceiptType").combogrid("getValue"),
                start: $("#AccountingReportDateFrom").datebox("getValue"), 
                end: $("#AccountingReportDateTo").datebox("getValue"),
                rows: dg.rows, 
                footer: $("#tb-accounting-report-receipt-other").datagrid("getFooterRows")
            }
            exportDocument("{{ url('finance/report/receipt/other/export-') }}" + document,payload,"Ekspor Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>