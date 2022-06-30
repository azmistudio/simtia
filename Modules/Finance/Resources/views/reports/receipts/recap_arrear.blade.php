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
                <label class="mb-1" style="width:80px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Tahun Ajaran:</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-recap-arrear">
                <input type="hidden" id="id-report-recap-arrear-department" value="-1" /> 
                <input type="hidden" id="id-report-recap-arrear-grade" value="-1" /> 
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportGrade" class="easyui-textbox tbox" style="width:80px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportClass" class="easyui-combogrid cgrd" style="width:150px;height:22px;" data-options="
                    panelWidth: 450,
                    idField: 'id',
                    textField: 'class',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                        {field:'class',title:'Kelas',width:150},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportSchoolYear" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:90,valueField:'id',textField:'text'"></select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-recap-arrear').form('reset');filterAccountingReportClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
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
        $("#AccountingReportClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/only/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#AccountingReportGrade").textbox('setText', row.grade)
                $("#AccountingReportClass").combogrid('hidePanel')
                $("#id-report-recap-arrear-department").val(row.department_id)
                $("#id-report-recap-arrear-grade").val(row.grade_id)
            }
        })
        $("#AccountingReportSchoolYear").combobox("clear").combobox("reload", "{{ url('academic/school-year/combo-box') }}" + "/1" + "?_token=" + "{{ csrf_token() }}")
    })
    function filterAccountingReportClass(val) {
        if (val > 0) {
            if ($("#AccountingReportSchoolYear").combobox("getValue") > 0) {
                $("#p-report-receipt").panel("refresh", "{{ url('finance/report/receipt/recap/arrear/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&class_id=" + $("#AccountingReportClass").combogrid("getValue") 
                    + "&class=" + $("#AccountingReportClass").combogrid("getText") 
                    + "&department_id=" + $("#id-report-recap-arrear-department").val()
                    + "&department=" + $("#AccountingReportDept").textbox("getText") 
                    + "&grade_id=" + $("#id-report-recap-arrear-grade").val()
                    + "&grade=" + $("#AccountingReportGrade").textbox("getText") 
                    + "&schoolyear_id=" + $("#AccountingReportSchoolYear").combobox("getValue")
                    + "&schoolyear=" + $("#AccountingReportSchoolYear").combobox("getText")
                )
            }
        } else {
            $("#p-report-receipt").panel("clear")
        }
    }
</script>