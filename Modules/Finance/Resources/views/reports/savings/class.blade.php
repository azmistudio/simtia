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
                <label class="mb-1" style="width:120px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:90px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:350px;">Jenis Tabungan:</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-saving-class">
                <input type="hidden" id="id-report-saving-class-department" value="-1" /> 
                <input type="hidden" id="id-report-saving-class-schoolyear" value="-1" /> 
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:110px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportGrade" class="easyui-textbox tbox" style="width:120px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportSchoolYear" class="easyui-textbox tbox" style="width:90px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportClass" class="easyui-combogrid cgrd" style="width:120px;height:22px;" data-options="
                    panelWidth: 450,
                    idField: 'id',
                    textField: 'class',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                        {field:'class',title:'Kelas',width:150},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AccountingReportSavingType" class="easyui-combobox cbox" style="width:350px;height:22px;" data-options="panelHeight:90,valueField:'id',textField:'text'"></select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportSavingClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-saving-class').form('reset');filterAccountingReportSavingClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <table id="tb-accounting-report-saving-class" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="
                    toolbar:'#menubarSavingClass',method:'post',rownumbers:'true',showFooter:'true'">
                    <thead>
                        <tr>
                            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'">NIS</th>
                            <th data-options="field:'name',width:175,sortable:true,resizeable:true">Nama</th>
                            <th data-options="field:'class',width:115,resizeable:true,align:'center'">Kelas</th>
                            <th data-options="field:'balance',width:115,resizeable:true,align:'right'">Saldo Tabungan</th>
                            <th data-options="field:'total_saving',width:115,resizeable:true,align:'right'">Total Setoran</th>
                            <th data-options="field:'last_saving',width:140,resizeable:true,align:'right'">Setoran Terakhir</th>
                            <th data-options="field:'total_withdraw',width:115,resizeable:true,align:'right'">Total Tarikan</th>
                            <th data-options="field:'last_withdraw',width:140,resizeable:true,align:'right'">Tarikan Terakhir</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="menubarSavingClass">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAccountingReportSavingClass('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAccountingReportSavingClass('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AccountingReportPayment").combobox("clear")
                $("#AccountingReportDept").textbox('setText', row.department)
                $("#AccountingReportGrade").textbox('setText', row.grade)
                $("#AccountingReportSchoolYear").textbox('setText', row.school_year)
                $("#AccountingReportClass").combogrid('hidePanel')
                $("#id-report-saving-class-department").val(row.department_id)
                $("#id-report-saving-class-schoolyear").val(row.school_year)
                $("#AccountingReportSavingType").combobox("clear").combobox("reload", "{{ url('finance/saving/student/type/combo-box') }}" + "?_token=" + "{{ csrf_token() }}" + "&is_employee=0&department_id=" + row.department_id)
            }
        })
        $("#tb-accounting-report-saving-class").datagrid()
    })
    function filterAccountingReportSavingClass(val) {
        if (val > 0) {
            if ($("#AccountingReportSavingType").combobox("getValue") > 0) {
                $("#tb-accounting-report-saving-class").datagrid("reload", "{{ url('finance/report/saving/class/data') }}" 
                    + "?_token=" + "{{ csrf_token() }}" 
                    + "&class_id=" + $("#AccountingReportClass").combogrid("getValue") 
                    + "&class=" + $("#AccountingReportClass").combogrid("getText") 
                    + "&schoolyear=" + $("#id-report-saving-class-schoolyear").val()
                    + "&department_id=" + $("#id-report-saving-class-department").val()
                    + "&department=" + $("#AccountingReportDept").textbox("getText") 
                    + "&grade=" + $("#AccountingReportGrade").textbox("getText") 
                    + "&saving_id=" + $("#AccountingReportSavingType").combobox("getValue") 
                    + "&saving=" + $("#AccountingReportSavingType").combobox("getText") 
                    + "&is_employee=0" 
                )
            }
        } else {
            $("#tb-accounting-report-saving-class").datagrid("loadData", [])
        }
    }
    function exportAccountingReportSavingClass(document) {
        var dg = $("#tb-accounting-report-saving-class").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                grade: $("#AccountingReportGrade").textbox("getText"),
                department: $("#AccountingReportDept").textbox("getText"),
                department_id: $("#id-report-saving-class-department").val(),
                schoolyear: $("#id-report-saving-class-schoolyear").val(),
                saving: $("#AccountingReportSavingType").combobox("getText"),
                saving_id: $("#AccountingReportSavingType").combobox("getValue"),
                rows: dg.rows,
                footers: dg.footer,
            }
            exportDocument("{{ url('finance/report/saving/class/export-') }}" + document,payload,"Ekspor Laporan ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>