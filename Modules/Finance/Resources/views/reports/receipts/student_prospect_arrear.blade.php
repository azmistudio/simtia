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
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:220px;">Proses:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:130px;">Kelompok:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Pembayaran</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Telat Bayar</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-prospect-arrear">
                <input type="hidden" id="id-report-prospect-arrear-department" value="-1" /> 
                <input type="hidden" id="id-report-prospect-arrear-admission" value="-1" /> 
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportAdmission" class="easyui-textbox tbox" style="width:220px;height:22px;" data-options="readonly:true" />
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
                <select id="AccountingReportPayment" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:90,valueField:'id',textField:'name'"></select>
                <span class="mr-2"></span>
                <input id="AccountingReportDuration" class="easyui-numberbox nbox" style="width:40px;height:22px;text-align: right;" value="30" />
                <span class="ml-2 mr-2">hari, dari</span>
                <input id="AccountingReportDateDelay" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportProspectArrear(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-prospect-arrear').form('reset');filterAccountingReportProspectArrear(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
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
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#AccountingReportAdmission").textbox('setText', row.admission_id)
                $("#id-report-prospect-arrear-department").val(row.department_id)
                $("#id-report-prospect-arrear-admission").val(row.admission)
                $("#AccountingReportProspectGroup").combogrid('hidePanel')
                $("#AccountingReportPayment").combobox("reload", "{{ url('finance/receipt/type/combo-box') }}" + "/3/" + row.department_id + "?_token=" + "{{ csrf_token() }}")
            }
        })
    })
    function filterAccountingReportProspectArrear(val) {
        if (val > 0) {
            if ($("#AccountingReportPayment").combobox("getValue") > 0) {
                $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/student/prospect/arrear/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&prospect_group_id=" + $("#AccountingReportProspectGroup").combogrid("getValue")
                    + "&prospect_group=" + $("#AccountingReportProspectGroup").combogrid("getText")
                    + "&admission_id=" + $("#id-report-prospect-arrear-admission").val()
                    + "&admission=" + $("#AccountingReportAdmission").textbox("getText")
                    + "&department_id=" + $("#id-report-prospect-arrear-department").val()
                    + "&department=" + $("#AccountingReportDept").textbox("getText") 
                    + "&payment=" + $("#AccountingReportPayment").combobox("getValue") 
                    + "&payment_name=" + $("#AccountingReportPayment").combobox("getText") 
                    + "&duration=" + $("#AccountingReportDuration").numberbox("getValue") 
                    + "&date_delay=" + $("#AccountingReportDateDelay").datebox("getValue") 
                    + "&status=0" 
                )
            }
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>