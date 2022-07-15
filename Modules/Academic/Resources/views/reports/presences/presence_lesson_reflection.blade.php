@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 333 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:130px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:190px;">Guru:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <form id="form-report-presence-lesson-reflection">
            <div class="col-12">
                <input type="hidden" id="id-report-presence-lesson-reflection-deptid" />
                <select id="AcademicReportSchoolyear" class="easyui-combobox cbox" style="width:130px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    @foreach ($schoolyears as $schoolyear)
                    <option value="{{ $schoolyear->id }}">{{ $schoolyear->is_active == 1 ? $schoolyear->school_year .' (A)' : $schoolyear->school_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <select id="AcademicReportTeacher" class="easyui-combogrid cgrd" style="width:190px;height:22px;" data-options="
                    panelWidth: 500,
                    idField: 'employee_id',
                    textField: 'employee',
                    fitColumns:true,
                    columns: [[
                        {field:'employee_no',title:'NIP',width:80},
                        {field:'employee',title:'Nama',width:200},
                        {field:'status',title:'Status',width:250},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AcademicReportSemester" class="easyui-combogrid cgrd" style="width:80px;height:22px;" data-options="
                    panelWidth: 250,
                    idField: 'id',
                    textField: 'semester',
                    fitColumns: true,
                    method: 'post',
                    columns: [[
                        {field:'department',title:'Departemen',width:150},
                        {field:'semester',title:'Semester',width:100},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AcademicReportGrade" class="easyui-combobox cbox" style="width:80px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    <option value="0">Semua</option>
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportPresenceLessonReflection(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-lesson-reflection').form('reset');filterAcademicReportPresenceLessonReflection(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
            </div>
            <div class="col-12 mt-1">
                <label class="mb-1" style="width:130px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:190px;">Pelajaran:</label>
            </div>
            <div class="col-12">
                <select id="AcademicReportClass" class="easyui-combobox cbox" style="width:130px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    <option value="0">Semua</option>
                </select>      
                <span class="mr-2"></span>          
                <select id="AcademicReportLesson" class="easyui-combobox cbox" style="width:190px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    <option value="0">Semua</option>
                </select>
            </div>
            </form>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-lesson-reflection" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,pagination:true,pageSize:50,pageList:[10,25,50,75,100],toolbar:'#toolbarAcademicReportPresenceLesson'">
                    <thead>
                        <tr>
                            <th data-options="field:'date',width:80,align:'center',sortable:true">Tanggal</th>
                            <th data-options="field:'time',width:60,align:'center'">Jam</th>
                            <th data-options="field:'class',width:150,resizeable:true">Kelas</th>
                            <th data-options="field:'status',width:150,align:'center'">Status</th>
                            <th data-options="field:'lesson',width:200,resizeable:true">Pelajaran</th>
                            <th data-options="field:'reflection',width:300,resizeable:true">Refleksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportPresenceLesson">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Refleksi Pengajar</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportPresenceLessonReflection('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportPresenceLessonReflection('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportTeacher").combogrid({
            url: '{{ url('academic/teacher/combo-grid/group') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
        })   
        $("#AcademicReportSemester").combogrid({
            url: '{{ url('academic/semester/combo-grid') }}',
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-lesson-reflection-deptid").val(row.department_id)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").combobox("reload", "{{ url('academic/report/grade/combo-box') }}" + "/" + row.department_id + "/1?_token=" + "{{ csrf_token() }}")
                $("#AcademicReportClass").combobox({ readonly : true })
                $("#AcademicReportGrade").combobox("setValue", "0")
                $("#AcademicReportLesson").combobox("reload", "{{ url('academic/report/lesson/combo-box') }}" + "/" + row.department_id + "/1?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#tb-report-presence-lesson-reflection").datagrid({
            onDblClickRow: function(index, row) {
                var payload = {
                    department: $("#AcademicReportDept").textbox("getValue"),
                    schoolyear: $("#AcademicReportSchoolyear").combobox("getValue"),
                    grade: $("#AcademicReportGrade").combobox("getText"),
                    class: $("#AcademicReportClass").combobox("getText"),
                    studentno: row.student_no,
                    student: row.student,
                    student_id: row.student_id,
                    class_id: $("#AcademicReportClass").combobox("getValue"),
                    start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                    end_date: $("#AcademicReportDateTo").datebox("getValue"),
                }
                exportDocument("{{ url('academic/report/presence/daily/export-') }}" + "pdf",payload,"Ekspor data Presensi Harian ke PDF","{{ csrf_token() }}")
            }
        })
        $("#AcademicReportGrade").combobox({
            onSelect: function(record) {
                if (record.value > 0) {
                    $("#AcademicReportClass").combobox({ readonly : false })
                    $("#AcademicReportClass").combobox("reload", "{{ url('academic/report/class/combo-box') }}" + "/" + record.value + "/" + $("#id-report-presence-lesson-reflection-schoolyearid").val() + "/1?_token=" + "{{ csrf_token() }}")
                } else {
                    $("#AcademicReportClass").combobox({ readonly : true })            
                }
            }
        })
        $("#AcademicReportClass").combobox({ readonly : true })
    })
    function filterAcademicReportPresenceLessonReflection(val) {
        if (val > 0) {
            $("#tb-report-presence-lesson-reflection").datagrid("reload", "{{ url('academic/report/presence/lesson/reflection/data') }}" 
                + "?_token=" + "{{ csrf_token() }}" 
                + "&department_id=" + $("#id-report-presence-lesson-reflection-deptid").val()
                + "&schoolyear_id=" + $("#AcademicReportSchoolyear").combobox("getValue")
                + "&employee_id=" + $("#AcademicReportTeacher").combogrid("getValue")
                + "&semester_id=" + $("#AcademicReportSemester").combogrid("getValue")
                + "&grade_id=" + $("#AcademicReportGrade").combobox("getValue")
                + "&lesson_id=" + $("#AcademicReportLesson").combobox("getValue")
                + "&class_id=" + $("#AcademicReportClass").combobox("getValue")
                + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
                + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
            )
        } else {
            $("#tb-report-presence-lesson-reflection").datagrid("loadData", [])
        }
    }
    function exportAcademicReportPresenceLessonReflection(document) {
        var dg = $("#tb-report-presence-lesson-reflection").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                department_id: $("#id-report-presence-lesson-reflection-deptid").val(),
                schoolyear: $("#AcademicReportSchoolyear").combobox("getText"),
                semester: $("#AcademicReportSemester").combogrid("getText"),
                semester_id: $("#AcademicReportSemester").combogrid("getValue"),
                grade: $("#AcademicReportGrade").combobox("getText"),
                grade_id: $("#AcademicReportGrade").combobox("getValue"),
                class: $("#AcademicReportClass").combobox("getText"),
                class_id: $("#AcademicReportClass").combobox("getValue"),
                lesson: $("#AcademicReportLesson").combobox("getText"),
                lesson_id: $("#AcademicReportLesson").combobox("getValue"),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/lesson/reflection/export-') }}" + document,payload,"Ekspor data Refleksi Pelajaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>