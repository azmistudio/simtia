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
                <label class="mb-1" style="width:50px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Thn.Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:65px;">Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:122px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Pelajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-lesson-class">
                <input type="hidden" id="id-report-presence-lesson-class-semester" value="-1" />
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:122px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportGrade" style="width:50px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSchoolYear" style="width:80px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSemester" style="width:65px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select name="student_id" id="AcademicReportClass" class="easyui-combogrid cgrd" style="width:122px;height:22px;" data-options="
                    panelWidth: 760,
                    idField: 'id',
                    textField: 'class',
                    fitColumns: true,
                    method: 'post',
                    columns: [[
                        {field:'department',title:'Departemen',width:100},
                        {field:'school_year',title:'Thn. Ajaran',width:80,align:'center'},
                        {field:'grade',title:'Tingkat',width:50,align:'center'},
                        {field:'semester',title:'Semester',width:50,align:'center'},
                        {field:'class',title:'Kelas',width:120},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AcademicReportLesson" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    <option value="0">Semua Pelajaran</option>
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportLessonPresenceClass()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-lesson-class').form('reset');filterAcademicReportLessonPresenceClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-lesson-class" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',showFooter:true,rownumbers:true,pagination:true,pageSize:50,pageList:[10,25,50,75,100],toolbar:'#toolbarAcademicReportLessonPresenceClass'">
                    <thead>
                        <tr>
                            <th data-options="field:'is_active',hidden:true">Aktif</th>
                            <th data-options="field:'student_no',width:80,sortable:true,align:'center'">NIS</th>
                            <th data-options="field:'student',width:150,resizeable:true">Nama</th>
                            <th data-options="field:'sum_present',width:80,resizeable:true,align:'center'">Jml.Hadir</th>
                            <th data-options="field:'sum_absent',width:80,align:'center'">Jml.Absen</th>
                            <th data-options="field:'sum_total',width:80,align:'center'">Jml.Total</th>
                            <th data-options="field:'sum_percent',width:50,align:'center'">%</th>
                            <th data-options="field:'mobile',width:100,align:'center'">No.HP</th>
                            <th data-options="field:'parent',width:200">Org. Tua/Wali</th>
                            <th data-options="field:'parent_mobile',width:200">HP Orang Tua</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportLessonPresenceClass">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Kehadiran Santri</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportLessonPresenceClass('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportLessonPresenceClass('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportClass").combogrid({
            url: "{{ url('academic/class/student/combo-grid') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-lesson-class-semester").val(row.semester_id)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").textbox("setValue", row.grade)
                $("#AcademicReportSchoolYear").textbox("setValue", row.school_year)
                $("#AcademicReportSemester").textbox("setValue", row.semester)      
                $("#AcademicReportLesson").combobox("reload", "{{ url('academic/report/lesson/combo-box') }}" + "/" + row.department_id + "/1?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#tb-report-presence-lesson-class").datagrid()
    })
    function filterAcademicReportLessonPresenceClass() {
        $("#tb-report-presence-lesson-class").datagrid("reload", "{{ url('academic/report/presence/lesson/class/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&class_id=" + $("#AcademicReportClass").combogrid("getValue")
            + "&semester_id=" + $("#id-report-presence-lesson-class-semester").val()
            + "&lesson_id=" + $("#AcademicReportLesson").combogrid("getValue")
            + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
        )
    }
    function exportAcademicReportLessonPresenceClass(document) {
        var dg = $("#tb-report-presence-lesson-class").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                schoolyear: $("#AcademicReportSchoolYear").textbox("getValue"),
                grade: $("#AcademicReportGrade").textbox("getValue"),
                class: $("#AcademicReportClass").combogrid("getText"),
                lesson: $("#AcademicReportLesson").combogrid("getText"),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/lesson/class/export-') }}" + document,payload,"Ekspor data Presensi Pelajaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>