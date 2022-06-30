@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:130px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-lesson-absent">
                <input type="hidden" id="id-report-presence-lesson-absent-deptid" />
                <select id="AcademicReportSchoolyear" class="easyui-combobox cbox" style="width:130px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    @foreach ($schoolyears as $schoolyear)
                    <option value="{{ $schoolyear->id }}">{{ $schoolyear->is_active == 1 ? $schoolyear->school_year .' (A)' : $schoolyear->school_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AcademicReportSemester" class="easyui-combogrid cgrd" style="width:100px;height:22px;" data-options="
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
                <select id="AcademicReportGrade" class="easyui-combobox cbox" style="width:110px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    <option value="0">Semua</option>
                </select>
                <span class="mr-2"></span>
                <select id="AcademicReportClass" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    <option value="0">Semua</option>
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportPresenceAbsentLesson(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-lesson-absent').form('reset');filterAcademicReportPresenceAbsentLesson(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-lesson-absent" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,pagination:true,pageSize:50,pageList:[10,25,50,75,100],singleSelect:'true',toolbar:'#toolbarAcademicReportPresenceLesson'">
                    <thead>
                        <tr>
                            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'">NIS</th>
                            <th data-options="field:'student',width:150,resizeable:true">Nama</th>
                            <th data-options="field:'class',width:130,resizeable:true">Kelas</th>
                            <th data-options="field:'lesson',width:150,resizeable:true">Pelajaran</th>
                            <th data-options="field:'date',width:80,align:'center'">Tanggal</th>
                            <th data-options="field:'presence',width:100,align:'center'">Presensi</th>
                            <th data-options="field:'remark',width:150,resizeable:true">Keterangan</th>
                            <th data-options="field:'mobile',width:100,align:'center'">No. HP</th>
                            <th data-options="field:'parent',width:200,resizeable:true">Orang Tua/Wali</th>
                            <th data-options="field:'parent_mobile',width:200,resizeable:true">HP Org.Tua</th>
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
                <span style="line-height: 25px;"><b>Data Santri yang tidak hadir</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportPresenceLessonAbsent('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportPresenceLessonAbsent('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportSemester").combogrid({
            url: '{{ url('academic/semester/combo-grid') }}',
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-lesson-absent-deptid").val(row.department_id)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").combobox("reload", "{{ url('academic/report/grade/combo-box') }}" + "/" + row.department_id + "/1?_token=" + "{{ csrf_token() }}")
                $("#AcademicReportClass").combobox({ readonly : true })
                $("#AcademicReportGrade").combobox("setValue", "0")
            }
        })
        $("#tb-report-presence-lesson-absent").datagrid({
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
                    $("#AcademicReportClass").combobox("reload", "{{ url('academic/report/class/combo-box') }}" + "/" + record.value + "/" + $("#id-report-presence-lesson-absent-schoolyearid").val() + "/1?_token=" + "{{ csrf_token() }}")
                } else {
                    $("#AcademicReportClass").combobox({ readonly : true })            
                }
            }
        })
        $("#AcademicReportClass").combobox({ readonly : true })
    })
    function filterAcademicReportPresenceAbsentLesson(val) {
        if (val > 0) {
            $("#tb-report-presence-lesson-absent").datagrid("reload", "{{ url('academic/report/presence/lesson/absent/data') }}" 
                + "?_token=" + "{{ csrf_token() }}" 
                + "&department_id=" + $("#id-report-presence-lesson-absent-deptid").val()
                + "&schoolyear_id=" +$("#AcademicReportSchoolyear").combobox("getValue")
                + "&semester_id=" + $("#AcademicReportSemester").combogrid("getValue")
                + "&grade_id=" + $("#AcademicReportGrade").combobox("getValue")
                + "&class_id=" + $("#AcademicReportClass").combobox("getValue")
                + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
                + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
            )
        } else {
            $("#tb-report-presence-lesson-absent").datagrid("loadData", [])
        }
    }
    function exportAcademicReportPresenceLessonAbsent(document) {
        var dg = $("#tb-report-presence-lesson-absent").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                department_id: $("#id-report-presence-lesson-absent-deptid").val(),
                schoolyear: $("#AcademicReportSchoolyear").combobox("getText"),
                schoolyear_id: $("#AcademicReportSchoolyear").combobox("getValue"),
                semester: $("#AcademicReportSemester").combogrid("getText"),
                semester_id: $("#AcademicReportSemester").combogrid("getValue"),
                grade: $("#AcademicReportGrade").combobox("getText"),
                grade_id: $("#AcademicReportGrade").combobox("getValue"),
                class: $("#AcademicReportClass").combobox("getText"),
                class_id: $("#AcademicReportClass").combobox("getValue"),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/lesson/absent/export-') }}" + document,payload,"Ekspor Presensi Pelajaran Absen ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>