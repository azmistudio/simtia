@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 18 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
    $GridHeightSub = $InnerHeight - 342 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Thn. Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Pelajaran:</label>
            </div>
            <div class="col-12">
                <form id="form-report-avg-lesson-plan-student">
                <input type="hidden" id="id-report-avg-lesson-plan-student-deptid" />
                <input type="hidden" id="id-report-avg-lesson-plan-student-semester" />
                <input type="hidden" id="id-report-avg-lesson-plan-student-lesson" />
                <input type="hidden" id="id-report-avg-lesson-plan-student" />
                <input type="hidden" id="id-report-avg-lesson-plan-student-exam" />
                <input type="hidden" id="id-report-avg-lesson-plan-student-name" />
                <input type="hidden" id="id-report-avg-lesson-plan-student-schoolyearid" />
                <input type="text" id="AcademicReportDepartment" class="easyui-textbox tbox" style="width:150px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportSchoolyear" class="easyui-textbox tbox" style="width:100px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportSemester" class="easyui-textbox tbox" style="width:80px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <select name="grade_id" id="AcademicReportGrade" class="easyui-combogrid cgrd" style="width:100px;height:22px;" data-options="
                    panelWidth: 450,
                    idField: 'id',
                    textField: 'grade',
                    fitColumns: true,
                    method: 'post',
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'school_year',title:'Thn. Ajaran',width:120,align:'center'},
                        {field:'semester',title:'Semester',width:100,align:'center'},
                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AcademicReportClass" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'"></select>
                <span class="mr-2"></span>
                <select id="AcademicReportLesson" class="easyui-combobox cbox" style="width:200px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'"></select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportAssessmentLessonStudentStat(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-avg-lesson-plan-student').form('reset');filterAcademicReportAssessmentLessonStudentStat(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-3">
                <br/>
                <div id="dl-avg-lesson-plan-student" class="easyui-datalist" title="RPP" lines="true" style="width:100%;height:{{ $GridHeight }}"></div>
            </div>
            <div class="col-9">
                <br/>
                <div style="width:100%;height:{{ $GridHeight }};border: solid 1px #d5d5d5;padding-top:10px;">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-9">
                                <select id="AcademicReportAssessmentType" class="easyui-combobox cbox" style="width:335px;height:22px;" data-options="label:'Jenis Pengujian:',labelWidth:'125px',labelPosition:'before',panelHeight:68,valueField:'value',textField:'text'"></select>
                                <span id="loader-graph-report" class="panel-loading" style="visibility:hidden;padding-top: 9px;"></span>
                            </div>
                            <div class="col-3 text-right">
                                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportAssessmentLessonStudentStat('pdf')">Ekspor PDF</a>
                            </div>
                            <div class="col-4 pr-2">
                                <table id="tb-avg-lesson-plan-student-data" style="width:100%;height:{{ $GridHeightSub }};" data-options="">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'student_no',width:80,align:'center'">NIS</th>
                                            <th data-options="field:'student',width:120,resizeable:true">Nama</th>
                                            <th data-options="field:'total',width:80,align:'center'">Rata - Rata</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="col-8 pl-2">
                                <div id="AcademicReportAssessmentGraph" style="width:100%;height:{{ $GridHeightSub }};border: solid 1px #d5d5d5;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        var dgavgplanstudent = $("#tb-avg-lesson-plan-student-data").datagrid()
        $("#AcademicReportClass").combobox({ readonly : true })
        $("#AcademicReportGrade").combogrid({
            url: '{{ url('academic/grade/combo-grid') }}',
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-avg-lesson-plan-student-deptid").val(row.department_id)
                $("#id-report-avg-lesson-plan-student-semester").val(row.semester_id)
                $("#id-report-avg-lesson-plan-student-schoolyearid").val(row.schoolyear_id)
                $("#AcademicReportDepartment").textbox("setValue",row.department)
                $("#AcademicReportSchoolyear").textbox("setValue",row.school_year)
                $("#AcademicReportSemester").textbox("setValue",row.semester)
                $("#AcademicReportLesson").combobox("clear")
                $("#AcademicReportLesson").combobox("reload", "{{ url('academic/report/lesson/combo-box') }}" + "/" + row.department_id + "/0?_token=" + "{{ csrf_token() }}")
                $("#AcademicReportClass").combobox({ readonly : false })
                $("#AcademicReportClass").combobox("reload", "{{ url('academic/report/class/combo-box') }}" + "/" + row.id + "/" + row.schoolyear_id + "/0?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#dl-avg-lesson-plan-student").datalist({
            onDblClickRow: function(index,row) {
                var rpps = row.value.split("-")
                $("#id-report-avg-lesson-plan-student-name").val(row.text)
                $("#id-report-avg-lesson-plan-student").val(rpps[0])
                $("#id-report-avg-lesson-plan-student-lesson").val(rpps[1])
                $("#AcademicReportAssessmentType").combobox("clear")
                $("#loader-graph-report").css("visibility","visible")
                $("#AcademicReportAssessmentType").combobox("reload", "{{ url('academic/lesson/exam/combo-box') }}" + "/" + rpps[1] + "?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#AcademicReportAssessmentType").combobox({
            onLoadSuccess: function() {
                $("#loader-graph-report").css("visibility","hidden")
            },
            onSelect: function(record) {
                $("#loader-graph-report").css("visibility","visible")
                $("#id-report-avg-lesson-plan-student-exam").val(record.value)
                $.post("{{ url('academic/report/assessment/plan/average/student/graph') }}", { 
                    _token: "{{ csrf_token() }}",  
                    semester_id: $("#id-report-avg-lesson-plan-student-semester").val(),
                    lesson_exam_id: record.value,
                    lesson_plan_id: $("#id-report-avg-lesson-plan-student").val(),
                    lesson_id: $("#id-report-avg-lesson-plan-student-lesson").val(),
                    grade_id: $("#AcademicReportGrade").combogrid("getValue"),
                    class_id: $("#AcademicReportClass").combobox("getValue"),
                }, function (response) {
                    $("#AcademicReportAssessmentGraph").html(response[1])
                    dgavgplanstudent.datagrid({
                        data: response[0]
                    })
                    $("#loader-graph-report").css("visibility","hidden")
                })
            }
        })
    })
    function filterAcademicReportAssessmentLessonStudentStat(val) {
        if (val > 0) {
            $("#dl-avg-lesson-plan-student").datalist("reload", "{{ url('academic/report/assessment/plan/average/class/data') }}" 
                + "?_token=" + "{{ csrf_token() }}" 
                + "&department_id=" + $("#id-report-avg-lesson-plan-student-deptid").val()
                + "&semester_id=" + $("#id-report-avg-lesson-plan-student-semester").val()
                + "&grade_id=" + $("#AcademicReportGrade").combobox("getValue")
                + "&lesson_id=" + $("#AcademicReportLesson").combobox("getValue")
            )
        } else {
            $("#dl-avg-lesson-plan-student").datalist("loadData", [])
            $("#tb-avg-lesson-plan-student-data").datagrid("loadData", [])
            $("#AcademicReportAssessmentGraph").html("")
            $("#AcademicReportAssessmentType").combobox("loadData", [])
            $("#AcademicReportAssessmentType").combobox("clear")
        }
    }
    function exportAcademicReportAssessmentLessonStudentStat(document) {
        var dg = $("#tb-avg-lesson-plan-student-data").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDepartment").textbox("getValue"),
                department_id: $("#id-report-avg-lesson-plan-student-deptid").val(),
                schoolyear: $("#AcademicReportSchoolyear").textbox("getText"),
                semester: $("#AcademicReportSemester").combogrid("getText"),
                semester_id: $("#id-report-avg-lesson-plan-student-semester").val(),
                grade: $("#AcademicReportGrade").combobox("getText"),
                grade_id: $("#AcademicReportGrade").combobox("getValue"),
                class: $("#AcademicReportClass").combobox("getText"),
                class_id: $("#AcademicReportClass").combobox("getValue"),
                lesson_exam_id: $("#id-report-avg-lesson-plan-student-exam").val(),
                lesson_plan_id: $("#id-report-avg-lesson-plan-student").val(),
                lesson_id: $("#id-report-avg-lesson-plan-student-lesson").val(),
                lesson: $("#AcademicReportLesson").combobox("getText"),
                lesson_exam: $("#AcademicReportAssessmentType").combobox("getText"),
                lesson_plan: $("#id-report-avg-lesson-plan-student-name").val(),
            }
            exportDocument("{{ url('academic/report/assessment/plan/average/student/export-') }}" + document,payload,"Ekspor Rata-Rata RPP Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>