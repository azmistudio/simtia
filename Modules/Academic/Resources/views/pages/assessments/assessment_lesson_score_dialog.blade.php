@php
    $lesson_exam = '';
@endphp
<div class="container-fluid">
	<div class="row">
        <div class="col-12 p-2">
            <div class="mb-1">
                <input class="easyui-textbox" id="assessmentLessonScoreDialogDept" value="{{ $infos->department }}" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreDialogGrade" value="{{ $infos->grade }}" style="width:300px;height:22px;" data-options="label:'Tingkat:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreDialogSchoolYear" value="{{ $infos->school_year }}" style="width:300px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'120px',readonly:true" />
            </div>
            <div class="mb-1">
                <input class="easyui-textbox" id="assessmentLessonScoreDialogSemester" value="{{ $infos->semester }}" style="width:300px;height:22px;" data-options="label:'Semester:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreDialogTeacher" value="{{ $infos->teacher }}" style="width:300px;height:22px;" data-options="label:'Guru:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreDialogLesson" value="{{ $infos->lesson }}" style="width:300px;height:22px;" data-options="label:'Pelajaran:',labelWidth:'120px',readonly:true" />
            </div>
            <div class="mb-1">
                <input class="easyui-textbox" id="assessmentLessonScoreDialogAspectScore" value="{{ $infos->score_aspect }}" style="width:300px;height:22px;" data-options="label:'Aspek Penilaian:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreDialogAssessment" value="{{ $infos->assessment }}" style="width:300px;height:22px;" data-options="label:'Jenis Pengujian:',labelWidth:'120px',readonly:true" />
            </div>
			<div class="mt-3 mb-1 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Save'" onclick="saveCalcAssessmentLessonScoreDialog()" style="height:22px;">Simpan Nilai Akhir</a>
            </div>
			<table id="tb-assessment-lesson-score-dialog" class="easyui-datagrid" style="width:100%;height:325px" data-options="method:'post',rownumbers:'true',showFooter:'true'">
                <thead>
                    <tr>
                        <th data-options="field:'student_no',width:90,resizeable:true,sortable:true,align:'center'">NIS</th>
                        <th data-options="field:'student',width:200,resizeable:true,sortable:true">Nama</th>
                        @php $i = 1; @endphp
                        @foreach ($exams as $row)
                        	<th field="{{ strtolower($row->getLessonExam->code) .'_'. $row->id }}" data-options="width: 80,align:'center',formatter:formatNumber">{{ $row->getLessonExam->code.'-'.$i }}</th>
                        @php $i++; $lesson_exam = $row->getLessonExam->code; @endphp
                        @endforeach
                        <th data-options="field:'avg_score',width:80,resizeable:true,align:'center',formatter:formatNumber">Rata-Rata</th>
                        <th data-options="field:'final_score',width:80,resizeable:true,editor:{type:'numberbox',options:{precision:2}},formatter:formatNumber">NA {{ $lesson_exam }}</th>
                    </tr>
                </thead>
            </table>
		</div>
	</div>
</div>
<script type="text/javascript">
    var dg = $("#tb-assessment-lesson-score-dialog")
	$(function () {
		dg.datagrid({
            url: "{{ url('academic/assessment/lesson/score/data') }}",
            queryParams: { _token: "{{ csrf_token() }}", assessment_id: "{{ $params[0] }}", class_id: "{{ $params[1] }}", semester_id: "{{ $params[2] }}" },
        })
        $("#tb-assessment-lesson-score-dialog").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 1,
            field: 'final_score'
        })
	})
    function formatNumber(val, row) {
        return parseFloat(val).toFixed(2)
    }
    function saveCalcAssessmentLessonScoreDialog() {
        var data = dg.datagrid('getData')
        $.post("{{ url('academic/assessment/lesson/score/final') }}", $.param({ 
            _token: "{{ csrf_token() }}", 
            method: "manual",
            students: JSON.stringify(data.rows), 
            lesson_id: {{ $infos->lesson_id }},
            class_id: {{ $infos->class_id }},
            semester_id: {{ $infos->semester_id }},
            lesson_exam_id: {{ $infos->lesson_exam_id }},
            assessment_id: {{ $infos->lesson_assessment_id }},
        }, true), function(response) {
            $.messager.alert('Informasi', response.message)
            $("#tb-assessment-lesson-score").datagrid("reload")
            $("#assessment-lesson-score-w").window("close")
        })
    }
</script>