@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:225px;">Santri:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Thn. Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:130px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Pelajaran:</label>
            </div>
            <div class="col-12">
                <form id="form-report-student-exam-score-avg">
                <input type="hidden" id="id-report-student-exam-score-avg-deptid" />
                <input type="hidden" id="id-report-student-exam-score-avg-schoolyearid" />
                <input type="hidden" id="id-report-student-exam-score-avg-gradeid" />
                <input type="hidden" id="id-report-student-exam-score-avg-classid" />
                <input type="hidden" id="id-report-student-exam-score-avg-studentno" />
                <select name="student_id" id="AcademicReportStudent" class="easyui-combogrid cgrd" style="width:225px;height:22px;" data-options="
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
                <input type="text" id="AcademicReportDepartment" class="easyui-textbox tbox" style="width:150px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportSchoolyear" class="easyui-textbox tbox" style="width:80px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportGrade" class="easyui-textbox tbox" style="width:80px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportClass" class="easyui-textbox tbox" style="width:130px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <select id="AcademicReportLesson" class="easyui-combobox cbox" style="width:200px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'"></select>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportExamScoreAvg(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-student-exam-score-avg').form('reset');filterAcademicReportExamScoreAvg(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <div id="AcademicReportExamScoreAvgDetail" class="easyui-panel pnel" style="width:100%;height:{{ $GridHeight }};padding: 10px;overflow: hidden;"></div>
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
                $("#id-report-student-exam-score-avg-deptid").val(row.department_id)
                $("#id-report-student-exam-score-avg-schoolyearid").val(row.schoolyear_id)
                $("#id-report-student-exam-score-avg-gradeid").val(row.grade_id)
                $("#id-report-student-exam-score-avg-classid").val(row.class_id)
                $("#id-report-student-exam-score-avg-studentno").val(row.student_no)
                $("#AcademicReportDepartment").textbox("setValue", row.department)
                $("#AcademicReportSchoolyear").textbox("setValue", row.school_year)
                $("#AcademicReportGrade").textbox("setValue", row.grade)
                $("#AcademicReportClass").textbox("setValue", row.class)
                $("#AcademicReportLesson").combobox("clear")
                $("#AcademicReportLesson").combobox("reload", "{{ url('academic/report/lesson/combo-box') }}" + "/" + row.department_id + "/0?_token=" + "{{ csrf_token() }}")
            }
        })
        $("#tb-report-student-exam-score-avg").datagrid()
    })
    function filterAcademicReportExamScoreAvg(val) {
        if (val > 0) {
            $("#AcademicReportExamScoreAvgDetail").panel("refresh", "{{ url('academic/report/assessment/score/average/detail') }}"
                + "?student_id=" + $("#AcademicReportStudent").combogrid("getValue")
                + "&student_no=" + $("#id-report-student-exam-score-avg-studentno").val()
                + "&student=" + $("#AcademicReportStudent").combobox("getText")
                + "&department_id=" + $("#id-report-student-exam-score-avg-deptid").val()
                + "&schoolyear_id=" + $("#id-report-student-exam-score-avg-schoolyearid").val()
                + "&grade_id=" + $("#id-report-student-exam-score-avg-gradeid").val()
                + "&class_id=" + $("#id-report-student-exam-score-avg-classid").val()
                + "&lesson_id=" + $("#AcademicReportLesson").combobox("getValue")
                + "&lesson=" + $("#AcademicReportLesson").combobox("getText")
                + "&department=" + $("#AcademicReportDepartment").textbox("getText")
                + "&schoolyear=" + $("#AcademicReportSchoolyear").textbox("getText")
                + "&grade=" + $("#AcademicReportGrade").textbox("getText")
                + "&class=" + $("#AcademicReportClass").textbox("getText")
                + "&height=" + "{{ $GridHeight }}"
            )
        }
    }
</script>