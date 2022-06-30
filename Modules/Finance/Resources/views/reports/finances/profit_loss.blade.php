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
            <form id="form-accounting-report-profit-loss">
            <select name="bookyear_id" id="AccountingReportBookYear" class="easyui-combobox cbox" style="width:100px;height:22px;" data-options="panelHeight:68">
                @foreach ($bookyears as $bookyear)
                <option value="{{ $bookyear->id }}">{{ $bookyear->is_active == 1 ? $bookyear->book_year . ' (A)' : $bookyear->book_year }}</option>
                @endforeach
            </select>
            <span class="mr-2"></span>
            <input name="start" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
            <span class="mr-2"></span>
            <input name="end" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
            <span class="mr-2"></span>
            <input name="is_total" class="easyui-checkbox kbox" value="2" style="height:22px;" data-options="label:'Hanya Total',labelWidth:'80px',labelPosition:'after'" />
            <span class="mr-2"></span>
            <input name="is_zero" class="easyui-checkbox kbox" value="2" style="height:22px;" data-options="label:'Tampilkan Saldo Nol',labelWidth:'140px',labelPosition:'after',checked:'true'" />
            <span class="mr-2"></span>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportProfitLoss(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportProfitLoss(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
            </form>
        </div>
        <div class="col-3 text-right" style="top:-4px;">
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportProfitLossView', 'in')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomIn'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportProfitLossView', 'out')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomOut'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportProfitLossView', 'reset')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomToFit'"></a>
            <a href="javascript:void(0)" class="easyui-menubutton mbtn" data-options="menu:'#mm-report-profit-loss',menuAlign:'right'">Ekspor</a>
        </div>
        <div class="col-12">
            <div class="report-container" style="overflow-y: auto;height: {{ $PageHeight }};background-color: rgb(102, 102, 102);">
				<iframe id="AccountingReportProfitLossView" class="report-output" src="" frameborder="0" style="transform: scale(1.05);transform-origin: 0 0;height: calc(100%/ 1.05);width: calc(100%/ 1.05);"></iframe>
			</div>
        </div>
    </div>
</div>
{{-- menu --}}
<div id="mm-report-profit-loss">
    <div onclick="exportAccountingReportProfitLoss('pdf')">Ekspor PDF</div>
    <div onclick="exportAccountingReportProfitLoss('excel')">Ekspor Excel</div>
</div>
<script type="text/javascript">
    function filterAccountingReportProfitLoss(val) {
    	if (val === 1 && $("#AccountingReportBookYear").combobox("getValue") !== "") {
            $(".report-container").waitMe({effect : "facebook"})
            $.post("{{ url('finance/report/transaction/validate') }}" + "?_token=" + "{{ csrf_token() }}" + "&" + $("#form-accounting-report-profit-loss").serialize(), function(response) {
                if (response.success) {
                    $("#AccountingReportProfitLossView").attr("src", "{{ url('finance/report/profit-loss/view') }}" 
                        +"?"+ $("#form-accounting-report-profit-loss").serialize() 
                        +"&bookyear="+ $("#AccountingReportBookYear").textbox("getText")
                    )
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $(".report-container").waitMe("hide")
            },"json")
    	} else {
    		$("#form-accounting-report-profit-loss").form("reset")
    		$("#form-accounting-report-profit-loss").form("reset")
            $("#AccountingReportProfitLossView").attr("src", "")
    	}
    }
    function exportAccountingReportProfitLoss(document) {
        if ($("#AccountingReportBookYear").combobox("getValue") !== "") {
    	   var payload = {
                bookyear: $("#AccountingReportBookYear").textbox("getText"),
                form: $("#form-accounting-report-profit-loss").serializeArray(),
            }
            exportDocument("{{ url('finance/report/profit-loss/export-') }}" + document,payload,"Ekspor Laba/Rugi ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>