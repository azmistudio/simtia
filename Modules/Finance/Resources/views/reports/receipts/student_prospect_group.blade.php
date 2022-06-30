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
                <label class="mb-1" style="width:110px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:220px;">Proses:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:130px;">Kelompok:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:170px;">Jns.Penerimaan</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Pembayaran</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Status</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-prospect-group">
                <input type="hidden" id="id-report-prospect-group-department" value="-1" /> 
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:110px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportProcess" class="easyui-textbox tbox" style="width:220px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportProspectGroup" class="easyui-combogrid cgrd" style="width:130px;height:22px;" data-options="
                    panelWidth: 570,
                    idField: 'id',
                    textField: 'group',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'admission_id',title:'Proses',width:200},
                        {field:'group',title:'Kelompok',width:110},
                        {field:'quota',title:'Kapasitas/Terisi',width:100},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportReceiptCategory" class="easyui-combobox cbox" style="width:170px;height:22px;" data-options="panelHeight:90">
                    <option value="0">---</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportPayment" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:90,valueField:'id',textField:'name'"></select>
                <span class="mr-2"></span>
                <select id="AccountingReportStatus" class="easyui-combobox cbox" style="width:120px;height:22px;" data-options="panelHeight:90">
                    <option value="-1">-- Semua --</option>
                    <option value="0">Belum Lunas</option>
                    <option value="1">Lunas</option>
                    <option value="2">Gratis</option>
                </select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-prospect-group').form('reset');filterAccountingReportClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <div id="p-report-receipt" class="easyui-panel pnel" style="height:{{ $PanelHeight }};border:none !important;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportProspectGroup").combogrid('grid').datagrid({
            url: '{{ url('academic/admission/prospective-group/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AccountingReportPayment").combobox("clear")
                $("#AccountingReportProcess").textbox('setText', row.admission_id)
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#AccountingReportProspectGroup").combogrid('hidePanel')
                $("#id-report-prospect-group-department").val(row.department_id)
            }
        })
        $("#AccountingReportReceiptCategory").combobox({
            onSelect: function(record) {
                if (record.value !== "0" && $("#id-report-prospect-group-department").val() !== "-1") {
                    $("#AccountingReportPayment").combobox("clear").combobox("reload", "{{ url('finance/receipt/type/combo-box') }}" + "/" + record.value + "/" + $("#id-report-prospect-group-department").val() + "?_token=" + "{{ csrf_token() }}")
                }
            }
        })
    })
    function filterAccountingReportClass(val) {
        if (val > 0) {
            if ($("#AccountingReportPayment").combobox("getValue") > 0) {
                $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/student/prospect/group/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&prospect_group_id=" + $("#AccountingReportProspectGroup").combogrid("getValue") 
                    + "&prospect_group=" + $("#AccountingReportProspectGroup").combogrid("getText") 
                    + "&department_id=" + $("#id-report-prospect-group-department").val()
                    + "&department=" + $("#AccountingReportDept").combogrid("getText") 
                    + "&category_id=" + $("#AccountingReportReceiptCategory").combobox("getValue") 
                    + "&category=" + $("#AccountingReportReceiptCategory").combobox("getText") 
                    + "&payment=" + $("#AccountingReportPayment").combobox("getValue") 
                    + "&status=" + $("#AccountingReportStatus").combobox("getValue") 
                    + "&payment_name=" + $("#AccountingReportPayment").combobox("getText"))
            }
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>