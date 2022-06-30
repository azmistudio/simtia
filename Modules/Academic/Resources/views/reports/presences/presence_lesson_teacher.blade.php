@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:122px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:90px;">Thn.Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:125px;">Status:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:250px;">Pengajar:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-lesson-teacher">
                <input type="hidden" id="id-report-presence-lesson-teacher-schoolyearid" />
                <input type="hidden" id="id-report-presence-lesson-teacher-employeeid" />
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:122px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSchoolYear" style="width:90px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportStatus" style="width:125px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select name="employee_id" id="AcademicReportTeacher" class="easyui-combogrid cgrd" style="width:250px;height:22px;" data-options="
                    panelWidth: 570,
                    idField: 'seq',
                    textField: 'employee',
                    fitColumns: true,
                    method: 'post',
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                        {field:'employee',title:'Guru',width:200},
                        {field:'t_type',title:'Status',width:150},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportLessonTeacher()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-lesson-teacher').form('reset');filterAcademicReportLessonTeacher(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-lesson-teacher" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,pagination:true,pageSize:50,pageList:[10,25,50,75,100],toolbar:'#toolbarAcademicReportLessonTeacher'">
                    <thead>
                        <tr>
                            <th data-options="field:'date',width:80,sortable:true,align:'center'">Tanggal</th>
                            <th data-options="field:'time',width:80,resizeable:true,align:'center'">Jam</th>
                            <th data-options="field:'class',width:120,resizeable:true">Kelas</th>
                            <th data-options="field:'lesson',width:150,resizeable:true,align:'center'">Pelajaran</th>
                            <th data-options="field:'status',width:120,resizeable:true,align:'center'">Status</th>
                            <th data-options="field:'late',width:80,resizeable:true,align:'center'">Telat</th>
                            <th data-options="field:'times',width:60,resizeable:true,align:'center'">Jam</th>
                            <th data-options="field:'subject',width:200,resizeable:true">Materi</th>
                            <th data-options="field:'remark',width:200,resizeable:true">Keterangan</th>
                            <th data-options="field:'minutes',hidden:true">Menit</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportLessonTeacher">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Kehadiran Guru Pengajar</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportLessonTeacher('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportLessonTeacher('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportTeacher").combogrid({
            url: "{{ url('academic/presence/lesson/combo-grid') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-lesson-teacher-schoolyearid").val(row.schoolyear_id)
                $("#id-report-presence-lesson-teacher-employeeid").val(row.employee_id)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportSchoolYear").textbox("setValue", row.school_year)
                $("#AcademicReportStatus").textbox("setValue", row.t_type)
            }
        })
        $("#tb-report-presence-lesson-teacher").datagrid()
    })
    function filterAcademicReportLessonTeacher() {
        $("#tb-report-presence-lesson-teacher").datagrid("reload", "{{ url('academic/report/presence/lesson/teacher/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&employee_id=" + $("#id-report-presence-lesson-teacher-employeeid").val()
            + "&schoolyear_id=" + $("#id-report-presence-lesson-teacher-schoolyearid").val()
            + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
        )
    }
    function exportAcademicReportLessonTeacher(document) {
        var dg = $("#tb-report-presence-lesson-teacher").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                schoolyear: $("#AcademicReportSchoolYear").textbox("getValue"),
                employee: $("#AcademicReportTeacher").combogrid("getText"),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/lesson/teacher/export-') }}" + document,payload,"Ekspor data Presensi Pengajar ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>