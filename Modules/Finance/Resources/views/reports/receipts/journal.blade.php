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
                <label class="mb-1" style="width:145px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:250px;">Penerimaan:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Tahun Buku:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-receipt-journal">
                <input type="hidden" id="id-department-receipt-journal" value="-1" />
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:145px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportReceiptCategory" class="easyui-combogrid cgrd" style="width:250px;height:22px;" data-options="
                    panelWidth: 350,
                    idField: 'id',
                    textField: 'category',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:110},
                        {field:'category',title:'Kategori',width:240},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:90">
                    @foreach ($bookyears as $bookyear)
                    <option value="{{ $bookyear->id }}">{{ $bookyear->book_year }} @if ($bookyear->is_active == 1) (A) @endif</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportReceiptOther(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-receipt-journal').form('reset');filterAccountingReportReceiptOther(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <table id="tb-accounting-report-receipt-journal" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
                    toolbar:'#menubarReceiptJournal',method:'post',rownumbers:'true',pagination:'true',showFooter:'true',pageSize:50,pageList:[10,25,50,75,100]">
                    <thead>
                        <tr>
                            <th data-options="field:'cash_no',width:100,resizeable:true,align:'center'">Jurnal</th>
                            <th data-options="field:'trans_date',width:90,sortable:true,resizeable:true,align:'center'">Tanggal</th>
                            <th data-options="field:'transaction',width:350,resizeable:true">Transaksi</th>
                            <th data-options="field:'total',width:100,resizeable:true,align:'right'">Nilai</th>
                            <th data-options="field:'source',width:170,resizeable:true">Sumber</th>
                            <th data-options="field:'name',width:165,resizeable:true">Petugas</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="menubarReceiptJournal">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAccountingReportReceiptOther('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAccountingReportReceiptOther('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportReceiptCategory").combogrid({
            url: '{{ url('finance/receipt/type/payment/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#id-department-receipt-journal").val(row.department_id)
                $("#AccountingReportDept").textbox("setValue", row.department)
                $("#AccountingReportReceiptCategory").combogrid('hidePanel')
            }
        })
        $("#tb-accounting-report-receipt-journal").datagrid({
            view: detailview,
            detailFormatter:function(index,row){
                return '<div style="padding:2px;position:relative;"><table class="ddv"></table></div>'
            },
            onExpandRow: function(index,row){
                var ddv = $(this).datagrid("getRowDetail",index).find("table.ddv")
                ddv.datagrid({
                    url: '{{ url('finance/journal/data/detail') }}',
                    method: 'post',
                    queryParams: { _token: '{{ csrf_token() }}', journal_id: row.journal_id },
                    fitColumns:true,
                    singleSelect:true,
                    rownumbers:true,
                    loadMsg:'',
                    height:'auto',
                    columns:[[
                        {field:'code',title:'Kode',width:80,align:'center'},
                        {field:'name',title:'Nama akun',width:200},
                        {field:'debit',title:'Debit',width:100,align:'right',formatter:formatCurrencyVal},
                        {field:'credit',title:'Kredit',width:100,align:'right',formatter:formatCurrencyVal},
                    ]],
                    onResize:function(){
                        $("#tb-accounting-report-receipt-journal").datagrid("fixDetailRowHeight",index)
                    },
                    onLoadSuccess:function(){
                        setTimeout(function(){
                            $("#tb-accounting-report-receipt-journal").datagrid("fixDetailRowHeight",index)
                        },0)
                    }
                })
                $("#tb-accounting-report-receipt-journal").datagrid("fixDetailRowHeight",index)
            }
        })
    })
    function formatCurrencyVal(val, row) {
        return currencyFormat(val)
    }
    function filterAccountingReportReceiptOther(val) {
        if (val > 0) {
            if ($("#AccountingReportReceiptCategory").combogrid("getValue") !== "") {
                $("#tb-accounting-report-receipt-journal").datagrid("reload", "{{ url('finance/report/receipt/journal/data') }}" 
                    + "?_token=" + "{{ csrf_token() }}" 
                    + "&department_id=" + $("#id-department-receipt-journal").val()
                    + "&receipt_type_id=" + $("#AccountingReportReceiptCategory").combogrid("getValue")
                    + "&bookyear_id=" + $("#AccountingReportBookYear").combobox("getValue")
                    + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue")
                    + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue")
                )
            }
        } 
    }
    function exportAccountingReportReceiptOther(document) {
        var dg = $("#tb-accounting-report-receipt-journal").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AccountingReportDept").combobox("getText"),
                department_id: $("#id-department-receipt-journal").val(),
                receipt_type: $("#AccountingReportReceiptCategory").combogrid("getText"),
                receipt_type_id: $("#AccountingReportReceiptCategory").combogrid("getValue"),
                bookyear: $("#AccountingReportBookYear").combobox("getText"),
                bookyear_id: $("#AccountingReportBookYear").combobox("getValue"),
                start: $("#AccountingReportDateFrom").datebox("getValue"), 
                end: $("#AccountingReportDateTo").datebox("getValue"),
                rows: dg.rows,
            }
            exportDocument("{{ url('finance/report/receipt/journal/export-') }}" + document,payload,"Ekspor Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>