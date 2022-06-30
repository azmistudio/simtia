@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 282 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:100px;">Tahun Buku:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-trial-balance">
                <select name="bookyear_id" id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:100px;height:22px;" data-options="panelHeight:68">
                    @foreach ($bookyears as $bookyear)
                    <option value="{{ $bookyear->id }}">{{ $bookyear->is_active == 1 ? $bookyear->book_year . ' (A)' : $bookyear->book_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportTrialBalance($('#id-accounting-report-bookyear').val())" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-trial-balance').form('reset');filterAccountingReportTrialBalance(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-accounting-report-trial-balance" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,showFooter:true,pagination:false,toolbar:'#toolbarAccountingReportTrialBalance'">
                    <thead>
                        <tr>
                            <th data-options="field:'code',width:150,sortable:true,align:'center'">Kode Akun</th>
                            <th data-options="field:'name',width:350,resizeable:true">Nama</th>
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
<div id="toolbarAccountingReportTrialBalance">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Neraca Percobaan</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAccountintReportTrialBalance('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAccountintReportTrialBalance('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-accounting-report-trial-balance").datagrid()
    })
    function filterAccountingReportTrialBalance(bookyear_id) {
        if ( $("#AccountingReportBookYear").combobox("getValue") !== "" ) {
            $("#tb-accounting-report-trial-balance").datagrid("reload", "{{ url('finance/report/trial-balance/data') }}" 
                + "?_token=" + "{{ csrf_token() }}" 
                + "&bookyear_id=" + $("#AccountingReportBookYear").combobox("getValue")
                + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue")
                + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue")
            )
        }
    }
    function exportAccountintReportTrialBalance(document) {
        var dg = $("#tb-accounting-report-trial-balance").datagrid('getData')
        if (dg.rows.length > 0) {
            var payload = {
                bookyear: $("#AccountingReportBookYear").combobox("getText"),
                start: $("#AccountingReportDateFrom").datebox("getValue"), 
                end: $("#AccountingReportDateTo").datebox("getValue"),
                rows: dg.rows, 
                footer: $("#tb-accounting-report-trial-balance").datagrid("getFooterRows")
            }
            exportDocument("{{ url('finance/report/trial-balance/export-') }}" + document,payload,"Ekspor Neraca Percobaan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>