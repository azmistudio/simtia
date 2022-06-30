@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:132px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:70px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Thn.Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:170px;">Santri:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-daily">
                <input type="hidden" name="student_no" id="id-report-presence-daily-studentno" />
                <input type="hidden" name="class_id" id="id-report-presence-daily-classid" />
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:132px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportGrade" style="width:70px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSchoolYear" style="width:100px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportClass" style="width:120px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select name="student_id" id="AcademicReportStudent" class="easyui-combogrid cgrd" style="width:170px;height:22px;" data-options="
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
                ">
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportDailyPresence()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-daily').form('reset');filterAcademicReportDailyPresence(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-daily" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',showFooter:true,rownumbers:true,toolbar:'#toolbarAcademicReportDailyPresence'">
                    <thead>
                        <tr>
                            <th data-options="field:'date',width:200,sortable:true,align:'center'">Tanggal</th>
                            <th data-options="field:'semester',width:80,resizeable:true,align:'center'">Semester</th>
                            <th data-options="field:'class',width:150,resizeable:true">Kelas</th>
                            <th data-options="field:'present',width:80,align:'center'">Hadir</th>
                            <th data-options="field:'permit',width:80,align:'center'">Ijin</th>
                            <th data-options="field:'sick',width:80,align:'center'">Sakit</th>
                            <th data-options="field:'absent',width:80,align:'center'">Alpa</th>
                            <th data-options="field:'leave',width:80,align:'center'">Cuti</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportDailyPresence">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Presensi Harian</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportDailyPresence('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportDailyPresence('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportStudent").combogrid({
            url: "{{ url('academic/student/combo-grid') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-daily-classid").val(row.class_id)
                $("#id-report-presence-daily-studentno").val(row.student_no)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").textbox("setValue", row.grade)
                $("#AcademicReportSchoolYear").textbox("setValue", row.school_year)
                $("#AcademicReportClass").textbox("setValue", row.class)
            }
        })
        $("#tb-report-presence-daily").datagrid()
    })
    function filterAcademicReportDailyPresence() {
        $("#tb-report-presence-daily").datagrid("reload", "{{ url('academic/report/presence/daily/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&student_id=" + $("#AcademicReportStudent").combogrid("getValue")
            + "&class_id=" + $("#id-report-presence-daily-classid").val()
            + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
        )
    }
    function exportAcademicReportDailyPresence(document) {
        var dg = $("#tb-report-presence-daily").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                schoolyear: $("#AcademicReportSchoolYear").textbox("getValue"),
                grade: $("#AcademicReportGrade").textbox("getValue"),
                class: $("#AcademicReportClass").textbox("getValue"),
                studentno: $("#id-report-presence-daily-studentno").val(),
                student: $("#AcademicReportStudent").combogrid("getText"),
                student_id: $("#AcademicReportStudent").combogrid("getValue"),
                class_id: $("#id-report-presence-daily-classid").val(),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
                footer: $("#tb-report-presence-daily").datagrid("getFooterRows")
            }
            exportDocument("{{ url('academic/report/presence/daily/export-') }}" + document,payload,"Ekspor data Presensi Harian ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>