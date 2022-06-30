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
            <span class="mr-2"></span>
            <label class="mb-1" style="width:110px;">Tampilan:</label>

        </div>
        <div class="col-9">
            <form id="form-accounting-report-ledger">
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
            <select name="is_detail" class="easyui-combobox cbox" style="width:110px;height:22px;" data-options="panelHeight:68">
                <option value="1">Ringkasan</option>
                <option value="2">Rincian</option>
            </select>
            <span class="mr-2"></span>
            <input name="is_data" class="easyui-checkbox kbox" value="2" style="height:22px;" data-options="label:'Hanya memiliki transaksi',labelWidth:'165px',labelPosition:'after'" />
            <span class="mr-2"></span>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportLedger(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportLedger(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
            </form>
        </div>
        <div class="col-3 text-right" style="top:-4px;">
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportLedgerView', 'in')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomIn'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportLedgerView', 'out')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomOut'"></a>
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="zoomReport('AccountingReportLedgerView', 'reset')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--ZoomToFit'"></a>
            <a href="javascript:void(0)" class="easyui-menubutton mbtn" data-options="menu:'#mm-report-ledger',menuAlign:'right'">Ekspor</a>
        </div>
        <div class="col-12">
            <div class="report-container" style="overflow-y: auto;height: {{ $PageHeight }};background-color: rgb(102, 102, 102);">
				<iframe id="AccountingReportLedgerView" class="report-output" src="" frameborder="0" style="transform: scale(1.05);transform-origin: 0 0;height: calc(100%/ 1.05);width: calc(100%/ 1.05);"></iframe>
			</div>
        </div>
    </div>
</div>
{{-- menu --}}
<div id="mm-report-ledger">
    <div onclick="exportAccountingReportLedger('pdf')">Ekspor PDF</div>
    <div onclick="exportAccountingReportLedger('excel')">Ekspor Excel</div>
</div>
<script type="text/javascript">
    function filterAccountingReportLedger(val) {
    	if (val === 1 && $("#AccountingReportBookYear").combobox("getValue") !== "") {
            $(".report-container").waitMe({effect : "facebook"})
            $.post("{{ url('finance/report/transaction/validate') }}" + "?_token=" + "{{ csrf_token() }}" + "&" + $("#form-accounting-report-ledger").serialize(), function(response) {
                if (response.success) {
                    $("#AccountingReportLedgerView").attr("src", "{{ url('finance/report/ledger/view') }}" 
                        +"?"+ $("#form-accounting-report-ledger").serialize() 
                        +"&bookyear="+ $("#AccountingReportBookYear").textbox("getText")
                    )
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $(".report-container").waitMe("hide")
            },"json")
    	} else {
    		$("#form-accounting-report-ledger").form("reset")
            $("#AccountingReportLedgerView").attr("src", "")
    	}
    }
    function exportAccountingReportLedger(document) {
        if ($("#AccountingReportBookYear").combobox("getValue") !== "") {
    	   var payload = {
                bookyear: $("#AccountingReportBookYear").textbox("getText"),
                form: $("#form-accounting-report-ledger").serializeArray(),
            }
            exportDocument("{{ url('finance/report/ledger/export-') }}" + document,payload,"Ekspor Buku Besar ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>