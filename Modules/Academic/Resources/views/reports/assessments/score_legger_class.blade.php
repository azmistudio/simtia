@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
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
                <form id="form-report-score-legger-class">
                <input type="hidden" id="id-report-score-legger-class-deptid" />
                <input type="hidden" id="id-report-score-legger-class-semester" />
                <input type="hidden" id="id-report-score-legger-class-lesson" />
                <input type="hidden" id="id-report-score-legger-class" />
                <input type="hidden" id="id-report-score-legger-class-exam" />
                <input type="hidden" id="id-report-score-legger-class-name" />
                <input type="hidden" id="id-report-score-legger-class-schoolyearid" />
                <input type="text" id="AcademicReportDepartment" class="easyui-textbox tbox" style="width:150px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportSchoolyear" class="easyui-textbox tbox" style="width:100px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <input type="text" id="AcademicReportSemester" class="easyui-textbox tbox" style="width:80px;height:22px;" readonly="readonly" />
                <span class="mr-2"></span>
                <select name="grade_id" id="AcademicReportGrade" class="easyui-combogrid cgrd" style="width:100px;height:22px;" data-options="
                    panelWidth: 470,
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
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportScoreLeggerClass(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-score-legger-class').form('reset');filterAcademicReportScoreLeggerClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <div id="AcademicReportExamScoreLeggerClass" class="easyui-panel pnel" style="width:100%;height:{{ $GridHeight }};padding: 10px;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-score-legger-class-data").datagrid()
        $("#AcademicReportClass").combobox({ readonly : true })
        $("#AcademicReportGrade").combogrid({
            url: '{{ url('academic/grade/combo-grid') }}',
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-score-legger-class-deptid").val(row.department_id)
                $("#id-report-score-legger-class-semester").val(row.semester_id)
                $("#id-report-score-legger-class-schoolyearid").val(row.schoolyear_id)
                $("#AcademicReportDepartment").textbox("setValue",row.department)
                $("#AcademicReportSchoolyear").textbox("setValue",row.school_year)
                $("#AcademicReportSemester").textbox("setValue",row.semester)
                $("#AcademicReportLesson").combobox("clear")
                $("#AcademicReportLesson").combobox("reload", "{{ url('academic/report/lesson/combo-box') }}" + "/" + row.department_id + "/1?_token=" + "{{ csrf_token() }}")
                $("#AcademicReportClass").combobox({ readonly : false })
                $("#AcademicReportClass").combobox("reload", "{{ url('academic/report/class/combo-box') }}" + "/" + row.id + "/" + row.schoolyear_id + "/0?_token=" + "{{ csrf_token() }}")
            }
        })
    })
    function filterAcademicReportScoreLeggerClass(val) {
        if (val > 0) {
            $("#AcademicReportExamScoreLeggerClass").panel("refresh", "{{ url('academic/report/assessment/score/legger/class/view') }}"
                + "?lesson_id=" + $("#AcademicReportLesson").combobox("getValue")
                + "&semester_id=" + $("#id-report-score-legger-class-semester").val()
                + "&class_id=" + $("#AcademicReportClass").combobox("getValue")
                + "&schoolyear_id=" + $("#id-report-score-legger-class-schoolyearid").val()
                + "&department=" + $("#AcademicReportDepartment").textbox("getText")
                + "&schoolyear=" + $("#AcademicReportSchoolyear").textbox("getText")
                + "&class=" + $("#AcademicReportClass").combobox("getText")
                + "&semester=" + $("#AcademicReportSemester").textbox("getText")
                + "&lesson=" + $("#AcademicReportLesson").combobox("getText")
            )
        } else {
            $("#AcademicReportClass").combobox({ readonly : true })
            $("#AcademicReportClass").combobox("clear")
            $("#AcademicReportLesson").combobox("loadData", [])
            $("#AcademicReportLesson").combobox("clear")
            $("#AcademicReportExamScoreLeggerClass").panel("clear")
        }
    }
</script>