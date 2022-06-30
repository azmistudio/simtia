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
                <label class="mb-1" style="width:90px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:180px;">Santri</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal</label>
            </div>
            <div class="col-12">
                <form id="form-accounting-report-saving-student">
                <input type="hidden" id="AccountingReportStudentNo" />
                <input id="AccountingReportDept" class="easyui-textbox tbox" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportGrade" class="easyui-textbox tbox" style="width:80px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportSchoolYear" class="easyui-textbox tbox" style="width:90px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AccountingReportClass" class="easyui-textbox tbox" style="width:120px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AccountingReportStudent" class="easyui-combogrid cgrd" style="width:180px;height:22px;" data-options="
                    panelWidth: 760,
                    idField: 'id',
                    textField: 'student',
                    fitColumns: true,
                    method: 'post',
                    pagination: true,
                    pageSize:50,
                    pageList:[10,25,50,75,100],
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'grade',title:'Tingkat',width:60,align:'center'},
                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                        {field:'class',title:'Kelas',width:120},
                        {field:'student_no',title:'NIS',width:110,align:'center',sortable:true},
                        {field:'student',title:'Nama',width:200,sortable:true},
                    ]],
                "></select>
                <span class="mr-2"></span>
                <input id="AccountingReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AccountingReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAccountingReportSavingStudent(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-accounting-report-saving-student').form('reset');filterAccountingReportSavingStudent(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <div id="p-report-saving-student" class="easyui-panel pnel" style="height:{{ $PanelHeight }};"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AccountingReportStudent").combogrid({
            url: "{{ url('academic/student/combo-grid') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#AccountingReportDept").textbox("setValue", row.department)
                $("#AccountingReportGrade").textbox("setValue", row.grade)
                $("#AccountingReportSchoolYear").textbox("setValue", row.school_year)
                $("#AccountingReportClass").textbox("setValue", row.class)
                $("#AccountingReportStudentNo").val(row.student_no)
            }
        })
    })
    function filterAccountingReportSavingStudent(val) {
        if (val > 0) {
            if ($("#AccountingReportStudent").combogrid("getValue") > 0) {
                $("#p-report-saving-student").panel("refresh", "{{ url('finance/report/saving/student/view') }}" 
                    + "?w=" + "{{ $PanelHeight }}" + "." + "{{ $WindowWidth }}" + "&t=init" 
                    + "&is_prospect=0"
                    + "&student_id=" + $("#AccountingReportStudent").combogrid("getValue") 
                    + "&student=" + $("#AccountingReportStudent").combogrid("getText") 
                    + "&student_no=" + $("#AccountingReportStudentNo").val()
                    + "&department=" + $("#AccountingReportDept").textbox("getText") 
                    + "&grade=" + $("#AccountingReportGrade").textbox("getText") 
                    + "&class=" + $("#AccountingReportClass").textbox("getText") 
                    + "&start_date=" + $("#AccountingReportDateFrom").datebox("getValue") 
                    + "&end_date=" + $("#AccountingReportDateTo").datebox("getValue") 
                    + "&schoolyear=" + $("#AccountingReportSchoolYear").textbox("getText"))
            }
        } else {
            $("#p-report-saving-student").panel("clear")
        }
    }
</script>