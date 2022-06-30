@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $SubGridHeight = $InnerHeight - 236 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Nilai Rapor Santri</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div class="title">
            <h6><span id="mark-assessment-report-scores"></span>Kelas: <span id="title-assessment-report-scores"></span></h6>
        </div>
        <div id="page-assessment-report-scores" class="pt-3 pb-3">
            <form id="form-assessment-report-scores-main" method="post">
                <div class="container-fluid">
                    <div class="row row-cols-auto">
                        <div class="col-4">
                            <input type="hidden" id="id-assessment-report-scores" name="id" value="-1" />
                            <input type="hidden" id="id-assessment-report-scores-semester" name="semester_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-scores-employee" name="employee_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-scores-id" name="lesson_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-scores-exam" name="lesson_exam_id" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AssessmentReportScoresDept" style="width:380px;height:22px;" data-options="label:'Departemen:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AssessmentReportScoresGrade" style="width:380px;height:22px;" data-options="label:'Tingkat:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AssessmentReportScoresSchoolYear" style="width:380px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AssessmentReportScoresSemester" style="width:380px;height:22px;" data-options="label:'Semester:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="class_id" id="AssessmentReportScoresClass" class="easyui-combogrid" style="width:380px;height:22px;" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'170px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:120},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <label class="textbox-label textbox-label-before" style="text-align: left; width: 166px; height: 22px; line-height: 22px;">Presensi:</label>
                                <input id="assessmentReportScoresDaily" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Harian',labelPosition:'after'" />
                                <input id="assessmentReportScoresLesson" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Pelajaran',labelPosition:'after'" />
                            </div>
                            <div class="mb-1">
                                <input name="presence_date_start" id="AssessmentReportScoresStart" class="easyui-datebox" style="width:294px;height:22px;" data-options="label:'Tanggal Presensi (dari):',labelWidth:'170px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <input name="presence_date_end" id="AssessmentReportScoresEnd" class="easyui-datebox" style="width:294px;height:22px;" data-options="label:'Tanggal Presensi (sampai):',labelWidth:'170px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <div style="margin-left: 170px;" class="pt-1">
                                    <a href="javascript:void(0)" class="easyui-linkbutton" onclick="printClassReport()" style="height:22px;width: 210px;" data-options="iconCls:'ms-Icon ms-Icon--WordDocument'">Cetak Rapor Kelas</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-8">
                            <div>
                                <table id="tb-assessment-report-scores-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                    data-options="method:'post',toolbar:menubarAssessmentReportScores,rownumbers:'true',pagination:'true',singleSelect:true,pageSize:50,
                                        pageList:[10,25,50,75,100],remoteFilter:true,clientPaging:false">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'id',width:50,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,resizeable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:200,resizeable:true,align:'left'">Nama</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="assessment-report-scores-w" class="easyui-window" title="Rapor Santri" data-options="modal:true,closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--View'" style="width:100%;height:98%;padding:10px;"></div>
<script type="text/javascript">
    var titleAssessmentReportScores = document.getElementById("title-assessment-report-scores")
    var sgAssessmentReportScores = $("#tb-assessment-report-scores-student")
    var menubarAssessmentReportScores = [{
        text: 'Lihat Rapor Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var row = sgAssessmentReportScores.datagrid('getSelected')
            var start_date = $("#AssessmentReportScoresStart").datebox('getValue')
            var end_date = $("#AssessmentReportScoresEnd").datebox('getValue')
            if (start_date !== "" && end_date !== "" && row != null) {
                if ($("#AssessmentReportScoresClass").combobox('getValue') !== '') {
                    $("#assessment-report-scores-w").window("open").window('refresh', "{{ url('academic/assessment/report/score/student') }}" 
                        + "?class_id=" + $("#AssessmentReportScoresClass").combobox('getValue') 
                        + "&semester_id=" + $("#id-assessment-report-scores-semester").val() 
                        + "&student_no=" + row.student_no 
                        + "&student_name=" + row.name 
                        + "&student_id=" + row.id 
                        + "&daily=" + $("#assessmentReportScoresDaily").checkbox('options').checked
                        + "&lesson=" + $("#assessmentReportScoresLesson").checkbox('options').checked
                        + "&department=" + $("#AssessmentReportScoresDept").textbox('getText')
                        + "&grade=" + $("#AssessmentReportScoresGrade").textbox('getText')
                        + "&schoolyear=" + $("#AssessmentReportScoresSchoolYear").textbox('getText')
                        + "&semester=" + $("#AssessmentReportScoresSemester").textbox('getText')
                        + "&class=" + $("#AssessmentReportScoresClass").combobox('getText') 
                        + "&period_start=" + start_date
                        + "&period_end=" + end_date
                        + "&window_h=" + "{{ $InnerHeight }}"
                    )
                }
            } else {
                $.messager.alert('Peringatan', 'Tanggal Presensi harus diisi dan pilih salah satu Santri.', 'warning')
            }
        }
    },'-',{
        text: 'Cetak PDF',
        iconCls: 'ms-Icon ms-Icon--Print',
        handler: function() {
            var row = sgAssessmentReportScores.datagrid('getSelected')
            var start_date = $("#AssessmentReportScoresStart").datebox('getValue')
            var end_date = $("#AssessmentReportScoresEnd").datebox('getValue')
            if (start_date !== "" && end_date !== "" && row != null) {
                if ($("#AssessmentReportScoresClass").combobox('getValue') !== '') {
                    $.messager.progress({ title: "Ekspor dokumen ke PDF", msg: "Mohon tunggu..." })
                    $.post("{{ url('academic/assessment/report/score/student/export-pdf') }}", $.param({ 
                        _token: "{{ csrf_token() }}", 
                        class_id: $("#AssessmentReportScoresClass").combobox('getValue'),
                        semester_id: $("#id-assessment-report-scores-semester").val(),
                        student_no: row.student_no,
                        student_name: row.name,
                        student_id: row.id,
                        daily: $("#assessmentReportScoresDaily").checkbox('options').checked,
                        lesson: $("#assessmentReportScoresLesson").checkbox('options').checked,
                        department: $("#AssessmentReportScoresDept").textbox('getText'),
                        grade: $("#AssessmentReportScoresGrade").textbox('getText'),
                        schoolyear: $("#AssessmentReportScoresSchoolYear").textbox('getText'),
                        semester: $("#AssessmentReportScoresSemester").textbox('getText'),
                        class: $("#AssessmentReportScoresClass").combobox('getText') ,
                        period_start: $("#AssessmentReportScoresStart").datebox('getValue'),
                        period_end: $("#AssessmentReportScoresEnd").datebox('getValue'),
                    }, true), function(response) {
                        $.messager.progress("close")
                        window.open("/storage/downloads/" + response)
                    }).fail(function(){
                        $.messager.progress('close')
                        $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
                    })            
                }
            } else {
                $.messager.alert('Peringatan', 'Tanggal Presensi harus diisi dan pilih salah satu Santri.', 'warning')
            }
        }
    },'-',{
        text: 'Ekspor Ms. Word',
        iconCls: 'ms-Icon ms-Icon--WordDocument',
        handler: function() {
            var row = sgAssessmentReportScores.datagrid('getSelected')
            var start_date = $("#AssessmentReportScoresStart").datebox('getValue')
            var end_date = $("#AssessmentReportScoresEnd").datebox('getValue')
            if (start_date !== "" && end_date !== "" && row != null) {
                if ($("#AssessmentReportScoresClass").combobox('getValue') !== '') {
                    $.messager.progress({ title: "Ekspor dokumen ke Ms. Word", msg: "Mohon tunggu..." })
                    $.post("{{ url('academic/assessment/report/score/student/export-word') }}", $.param({ 
                        _token: "{{ csrf_token() }}", 
                        class_id: $("#AssessmentReportScoresClass").combobox('getValue'),
                        semester_id: $("#id-assessment-report-scores-semester").val(),
                        student_no: row.student_no,
                        student_name: row.name,
                        student_id: row.id,
                        daily: $("#assessmentReportScoresDaily").checkbox('options').checked,
                        lesson: $("#assessmentReportScoresLesson").checkbox('options').checked,
                        department: $("#AssessmentReportScoresDept").textbox('getText'),
                        grade: $("#AssessmentReportScoresGrade").textbox('getText'),
                        schoolyear: $("#AssessmentReportScoresSchoolYear").textbox('getText'),
                        semester: $("#AssessmentReportScoresSemester").textbox('getText'),
                        class: $("#AssessmentReportScoresClass").combobox('getText') ,
                        period_start: $("#AssessmentReportScoresStart").datebox('getValue'),
                        period_end: $("#AssessmentReportScoresEnd").datebox('getValue'),
                    }, true), function(response) {
                        $.messager.progress("close")
                        window.open("/storage/downloads/" + response)
                    }).fail(function(){
                        $.messager.progress('close')
                        $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
                    })            
                }
            } else {
                $.messager.alert('Peringatan', 'Tanggal Presensi harus diisi dan pilih salah satu Santri.', 'warning')
            }
        }
    }]
    $(function () {
        $("#AssessmentReportScoresClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AssessmentReportScoresDept").textbox('setValue', row.department)
                $("#AssessmentReportScoresGrade").textbox('setValue', row.grade)
                $("#AssessmentReportScoresSchoolYear").textbox('setValue', row.school_year)
                $("#AssessmentReportScoresSemester").textbox('setValue', row.semester)
                $("#id-assessment-report-scores-semester").val(row.semester_id)
                $("#title-assessment-report-scores").text(row.class)
                $("#AssessmentReportScoresClass").combogrid('hidePanel')
                $("#tb-assessment-report-scores-student").datagrid("load", {_token: "{{ csrf_token() }}", fclass: row.id})
                let periods = row.period.slice(1,-1).split("|")
                $("#AssessmentReportScoresStart").datebox().datebox('calendar').calendar({
                    validator: function(date){
                        var now = new Date();
                        let starts = periods[0].split("-")
                        let ends = periods[1].split("-")
                        var d1 = new Date(starts[0], parseInt(starts[1]) - 1, starts[2])
                        var d2 = new Date(ends[0], parseInt(ends[1]) - 1, ends[2])
                        return d1<=date && date<=d2;
                    }
                })
                $("#AssessmentReportScoresStart").datebox('setValue', row.start_date)
                $("#AssessmentReportScoresEnd").datebox().datebox('calendar').calendar({
                    validator: function(date){
                        var now = new Date();
                        let starts = periods[0].split("-")
                        let ends = periods[1].split("-")
                        var d1 = new Date(starts[0], parseInt(starts[1]) - 1, starts[2])
                        var d2 = new Date(ends[0], parseInt(ends[1]) - 1, ends[2])
                        return d1<=date && date<=d2;
                    }
                })
                $("#AssessmentReportScoresEnd").datebox('setValue', row.end_date)
            }
        })
        $("#tb-assessment-report-scores-student").datagrid({
            url: '{{ url('academic/student/list') }}',
            queryParams: { _token: '{{ csrf_token() }}', fclass: 0 }
        }).datagrid("enableFilter")
    })
    function printClassReport() {
        if ($("#AssessmentReportScoresClass").combobox('getValue') !== '') {
            $.messager.progress({ title: "Ekspor dokumen ke Ms. Word", msg: "Mohon tunggu..." })
            $.post("{{ url('academic/assessment/report/score/export-word') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                class_id: $("#AssessmentReportScoresClass").combobox('getValue'),
                semester_id: $("#id-assessment-report-scores-semester").val(),
                daily: $("#assessmentReportScoresDaily").checkbox('options').checked,
                lesson: $("#assessmentReportScoresLesson").checkbox('options').checked,
                department: $("#AssessmentReportScoresDept").textbox('getText'),
                grade: $("#AssessmentReportScoresGrade").textbox('getText'),
                schoolyear: $("#AssessmentReportScoresSchoolYear").textbox('getText'),
                semester: $("#AssessmentReportScoresSemester").textbox('getText'),
                class: $("#AssessmentReportScoresClass").combobox('getText') ,
                period_start: $("#AssessmentReportScoresStart").datebox('getValue'),
                period_end: $("#AssessmentReportScoresEnd").datebox('getValue'),
            }, true), function(response) {
                $.messager.progress("close")
                window.open("/storage/downloads/" + response)
            }).fail(function(){
                $.messager.progress('close')
                $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
            })            
        }
    }
</script>