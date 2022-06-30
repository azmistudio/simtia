@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $PageHeight = $InnerHeight - 282 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-12">
            <label class="mb-1" style="width:100px;">Tahun Buku:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
        </div>
        <div class="col-9">
            <form id="form-accounting-report-cash-flow">
            <select name="bookyear_id" id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:100px;height:22px;" data-options="panelHeight:68">
                @foreach ($bookyears as $bookyear)
                <option value="{{ $bookyear->id }}">{{ $bookyear->is_active == 1 ? $bookyear->book_year . ' (A)' : $bookyear->book_year }}</option>
                @endforeach
            </select>
            <span class="mr-2"></span>
            <input name="start" id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser,readonly:true" />
            <span class="mr-2"></span>
            <input name="end" id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
            <span class="mr-2"></span>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportCashFlow(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportCashFlow(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
            </form>
        </div>
        <div class="col-3 text-right" style="top:-4px;">
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportCashFlowView', 'in')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomIn'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportCashFlowView', 'out')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomOut'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportCashFlowView', 'reset')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomToFit'"></a>
            <a href="javascript:void(0)" class="easyui-menubutton mbtn" data-options="menu:'#mm-report-cash-flow',menuAlign:'right'">Ekspor</a>
        </div>
        <div class="col-12">
            <div class="report-container" style="overflow-y: auto;height: {{ $PageHeight }};background-color: rgb(102, 102, 102);">
				<iframe id="AccountingReportCashFlowView" class="report-output" src="" frameborder="0" style="transform: scale(1.05);transform-origin: 0 0;height: calc(100%/ 1.05);width: calc(100%/ 1.05);"></iframe>
			</div>
        </div>
    </div>
</div>
{{-- menu --}}
<div id="mm-report-cash-flow">
    <div onclick="exportAccountingReportCashFlow('pdf')">Ekspor PDF</div>
    <div onclick="exportAccountingReportCashFlow('excel')">Ekspor Excel</div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportBookYear").combobox({
            onSelect: function(record) {
                $.getJSON("{{ url('finance/book/year/show') }}" + "/" + record.value, function(data){
                    $("#AccountingReportDateFrom").datebox("setValue", data.start_date)
                })
            }
        })
    })
    function filterAccountingReportCashFlow(val) {
    	if (val === 1 && $("#AccountingReportBookYear").combobox("getValue") !== "") {
            $(".report-container").waitMe({effect : "facebook"})
            $.post("{{ url('finance/report/transaction/validate') }}" + "?_token=" + "{{ csrf_token() }}" + "&" + $("#form-accounting-report-cash-flow").serialize(), function(response) {
                if (response.success) {
                    $("#AccountingReportCashFlowView").attr("src", "{{ url('finance/report/cash-flow/view') }}" 
                        +"?"+ $("#form-accounting-report-cash-flow").serialize() 
                        +"&bookyear="+ $("#AccountingReportBookYear").combobox("getText")
                    )
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $(".report-container").waitMe("hide")
            },"json")
    	} else {
    		$("#form-accounting-report-cash-flow").form("reset")
            $("#AccountingReportCashFlowView").attr("src", "")
    	}
    }
    function exportAccountingReportCashFlow(document) {
        if ($("#AccountingReportBookYear").combobox("getValue") !== "") {
    	   var payload = {
                bookyear: $("#AccountingReportBookYear").combobox("getText"),
                form: $("#form-accounting-report-cash-flow").serializeArray(),
            }
            exportDocument("{{ url('finance/report/cash-flow/export-') }}" + document,payload,"Ekspor data Arus Kas ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>