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
                <label class="mb-1" style="width:150px;">Kelompok:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:180px;">Santri</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai tanggal</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-prospect">
                <input type="hidden" id="id-report-prospect-department" value="-1" /> 
                <input type="hidden" id="id-report-prospect-admission" value="-1" /> 
                <input type="hidden" id="id-report-prospect-group" value="-1" /> 
                <input type="hidden" id="report-prospect-registration" value="" /> 
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:110px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportProcess" class="easyui-textbox tbox" style="width:220px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportProcessGroup" class="easyui-textbox tbox" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportProspectStudent" class="easyui-combogrid cgrd" style="width:180px;height:22px;" data-options="
                    panelWidth: 760,
                    idField: 'id',
                    textField: 'name',
                    fitColumns: true,
                    method: 'post',
                    pagination: true,
                    pageSize:50,
                    pageList:[10,25,50,75,100],
                    columns: [[
                        {field:'department',title:'Departemen',width:110},
                        {field:'admission',title:'Proses',width:200,align:'center'},
                        {field:'group',title:'Kelompok',width:100,align:'center'},
                        {field:'registration_no',title:'No.Registrasi',width:110,align:'center',sortable:true},
                        {field:'name',title:'Nama',width:200,sortable:true},
                    ]],
                "></select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-prospect').form('reset');filterAccountingReportClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <div id="p-report-receipt" class="easyui-panel pnel" style="height:{{ $PanelHeight }};"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportProspectStudent").combogrid('grid').datagrid({
            url: '{{ url('academic/admission/prospective-student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#AccountingReportProcess").textbox('setText', row.admission)
                $("#AccountingReportProcessGroup").textbox('setText', row.group)
                $("#AccountingReportProspectStudent").combogrid('hidePanel')
                $("#id-report-prospect-department").val(row.department_id)
                $("#id-report-prospect-admission").val(row.admission_id)
                $("#id-report-prospect-group").val(row.prospect_group_id)
                $("#report-prospect-registration").val(row.registration_no)
            }
        })
    })
    function filterAccountingReportClass(val) {
        if (val > 0) {
            if ($("#AccountingReportProspectStudent").combogrid("getValue") > 0) {
                $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/student/prospect/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&prospect_group_id=" + $("#id-report-prospect-group").val()
                    + "&prospect_group=" + $("#AccountingReportProcessGroup").textbox("getText")
                    + "&prospect_id=" + $("#AccountingReportProspectStudent").combogrid("getValue") 
                    + "&admission_id=" + $("#id-report-prospect-admission").val()
                    + "&admission=" + $("#AccountingReportProcess").textbox("getText")
                    + "&department_id=" + $("#id-report-prospect-department").val()
                    + "&department=" + $("#AccountingReportDept").textbox("getText") 
                    + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue") 
                    + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue") 
                    + "&registration_no=" + $("#report-prospect-registration").val()
                    + "&student_id=" + $("#AccountingReportProspectStudent").combogrid("getValue") 
                    + "&student=" + $("#AccountingReportProspectStudent").combogrid("getText") 
                    + "&is_prospect=1"
                )
            }
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>