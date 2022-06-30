@php
    $GridHeight = $InnerHeight - 423 . "px";
    $SubGridHeight = $InnerHeight - 543 . "px";
    $lesson_exam = '';
@endphp
<div class="container-fluid">
	<div class="row">
        <div class="col-12">
            <div class="mb-1">
                <input class="easyui-textbox" id="assessmentLessonScoreDept" value="{{ $infos->department }}" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreGrade" value="{{ $infos->grade }}" style="width:300px;height:22px;" data-options="label:'Tingkat:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreSchoolYear" value="{{ $infos->school_year }}" style="width:300px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'120px',readonly:true" />
            </div>
            <div class="mb-1">
                <input class="easyui-textbox" id="assessmentLessonScoreSemester" value="{{ $infos->semester }}" style="width:300px;height:22px;" data-options="label:'Semester:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreTeacher" value="{{ $infos->teacher }}" style="width:300px;height:22px;" data-options="label:'Guru:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreLesson" value="{{ $infos->lesson }}" style="width:300px;height:22px;" data-options="label:'Pelajaran:',labelWidth:'120px',readonly:true" />
            </div>
            <div class="mb-1">
                <input class="easyui-textbox" id="assessmentLessonScoreAspectScore" value="{{ $infos->score_aspect }}" style="width:300px;height:22px;" data-options="label:'Aspek Penilaian:',labelWidth:'120px',readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox" id="assessmentLessonScoreAssessment" value="{{ $infos->assessment }}" style="width:300px;height:22px;" data-options="label:'Jenis Pengujian:',labelWidth:'120px',readonly:true" />
            </div>
        </div>
		<div class="col-8">
			<div class="mt-3 mb-1 text-right">
            	<a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportAssessmentLessonScore()" style="height:22px;">Ekspor ke Excel</a>
                <a href="javascript:void(0)" class="easyui-linkbutton small-btn" data-options="iconCls:'ms-Icon ms-Icon--Calculator'" onclick="recalcAssessmentLessonScore()" style="height:22px;">Hitung Ulang Rata-Rata Kelas & Santri</a>
            </div>
			<table id="tb-assessment-lesson-score" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="method:'post',rownumbers:'true',showFooter:'true',singleSelect:'true',toolbar:menubarAssessmentLessonScore">
                <thead>
                    <tr>
                        <th data-options="field:'student_no',width:90,resizeable:true,sortable:true,align:'center'">NIS</th>
                        <th data-options="field:'student',width:190,resizeable:true,sortable:true">Nama</th>
                        @php $i = 1; @endphp
                        @foreach ($exams as $row)
                        	<th field="{{ strtolower($row->getLessonExam->code).'_'.$row->id }}" data-options="width: 80,align:'center',formatter:formatNumber">{{ $row->getLessonExam->code.'-'.$i }}</th>
                        @php $i++; $lesson_exam = $row->getLessonExam->code; @endphp
                        @endforeach
                        <th data-options="field:'avg_score',width:80,resizeable:true,align:'center',formatter:formatNumber">Rata-Rata</th>
                        <th data-options="field:'final_score',width:80,resizeable:true,align:'center',formatter:formatNumber">NA {{ $lesson_exam }}</th>
                        <th data-options="field:'remark',width:120,resizeable:true">Perhitungan NA</th>
                        <th data-options="field:'id',hidden:true">ID</th>
                    </tr>
                </thead>
            </table>
		</div>
        <div class="col-4 mt-3">
            <p><b>Hitung Nilai Akhir {{ $lesson_exam }} berdasarkan:</b></p>
            <hr/>
            <p class="mb-2"><b>A. Perhitungan Manual</b></p>
            <span class=""><a href="javascript:void(0)" class="easyui-linkbutton small-btn text-left" data-options="iconCls:'ms-Icon ms-Icon--Calculator'" onclick="calcManualAssessmentLessonScore()" style="height:22px;width: 325px;">Hitung Manual Nilai Akhir {{ $lesson_exam }}</a></span>
            <p class="mt-2 mb-2"><b>B. Perhitungan Otomatis</b></p>
            <span class=""><a href="javascript:void(0)" class="easyui-linkbutton small-btn text-left" data-options="iconCls:'ms-Icon ms-Icon--Calculator'" onclick="calcAutoAssessmentLessonScore()" style="height:22px;width: 325px;">Hitung & Simpan Nilai Akhir {{ $lesson_exam }}</a></span>
            <div class="mb-2"></div>
            <table id="tb-assessment-lesson-score-value" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" data-options="method:'post',rownumbers:'true'">
                <thead>
                    <tr>
                        <th data-options="field:'ck',checkbox:true"></th>
                        <th data-options="field:'id',width:30,hidden:true">ID</th>
                        <th data-options="field:'weight_id',width:30,hidden:true">WeightID</th>
                        <th data-options="field:'assessment',width:180,resizeable:true">{{ $lesson_exam }}</th>
                        <th data-options="field:'score',width:65,editor:{type:'numberbox',options:{precision:2}}">Bobot(%)</th>
                        <th data-options="field:'lessonexam_id',width:30,hidden:true">Index</th>
                    </tr>
                </thead>
            </table>
        </div>
	</div>
</div>
{{-- dialog --}}
<div id="assessment-lesson-score-w" class="easyui-window" title="Hitung Manual Nilai Akhir {{ $lesson_exam }}" style="width:1024px;height:500px" data-options="iconCls:'ms-Icon ms-Icon--Calculator',modal:true,closed:true,maximizable:false,minimizable:false"></div>
<div id="assessment-lesson-score-edit-w" class="easyui-window p-2" title="Ubah Nilai" style="width:595px;" data-options="iconCls:'ms-Icon ms-Icon--Edit',resizable:true,modal:true,closed:true,maximizable:false,minimizable:false"></div>
<script type="text/javascript">
    var dg = $("#tb-assessment-lesson-score")
    var menubarAssessmentLessonScore = [{
        text: 'Ubah Nilai',
        iconCls: 'ms-Icon ms-Icon--Edit',
        handler: function() {
            var row = dg.datagrid('getSelected')
            if (row != null) {
                $("#assessment-lesson-score-edit-w").dialog("open")
                $("#assessment-lesson-score-edit-w").window("refresh", "{{ url('academic/assessment/lesson/dialog/edit/score') }}" 
                    + "?student_id=" + row.student_id 
                    + "&teachers_id=" + {{ $infos->teacher_id }}
                    + "&lesson_id=" + {{ $infos->lesson_id }}
                    + "&class_id=" + {{ $infos->class_id }}
                    + "&semester_id=" + {{ $infos->semester_id }}
                    + "&employee_id=" + {{ $infos->employee_id }}
                    + "&status_id=" + {{ $infos->status_id }}
                    + "&score_aspect_id=" + {{ $infos->score_aspect_id }}
                    + "&lesson_exam_id=" + {{ $infos->lesson_exam_id }}
                    + "&student_no=" + row.student_no
                    + "&student_name=" + row.student
                )
            } else {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            }
        }
    }]
	$(function () {
		dg.datagrid({
            url: "{{ url('academic/assessment/lesson/score/data') }}",
            queryParams: { _token: "{{ csrf_token() }}", assessment_id: "{{ $params[0] }}", class_id: "{{ $params[1] }}", semester_id: "{{ $params[2] }}" },
        })
        $("#tb-assessment-lesson-score-value").datagrid({
            url: "{{ url('academic/assessment/lesson/score/data/weight') }}",
            queryParams: { _token: "{{ csrf_token() }}", assessment_id: "{{ $params[0] }}", class_id: "{{ $params[1] }}", semester_id: "{{ $params[2] }}" },
        })
        $("#tb-assessment-lesson-score-value").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 1,
            field: 'score'
        })
	})
    function formatNumber(val, row) {
        if (val != null) {
            return parseFloat(val).toFixed(2)
        } else {
            return 0
        }
    }
    function recalcAssessmentLessonScore() {
        $.messager.confirm("Konfirmasi", "Anda akan menghitung ulang rata-rata kelas dan santri?", function (r) {
            if (r) {
                var dg = $("#tb-assessment-lesson-score").datagrid("getData")
                $.post("{{ url('academic/assessment/lesson/score/recalc') }}", $.param({ _token: "{{ csrf_token() }}", students: JSON.stringify(dg.rows), assessment_id: "{{ $params[0] }}", class_id: "{{ $params[1] }}", semester_id: "{{ $params[2] }}" }, true), function(response) {
                    $.messager.alert('Informasi', response.message)
                    $("#tb-assessment-lesson-score").datagrid("reload")
                })
            }
        })
    }
    function calcManualAssessmentLessonScore() {
        $("#assessment-lesson-score-w").window("open")
        $("#assessment-lesson-score-w").window("refresh", "{{ url('academic/assessment/lesson/dialog/score') }}" + "/" + "{{ $params[0] }}" + "-" + "{{ $params[1] }}" + "-" + "{{ $params[2] }}" + "-" + "{{ $params[3] }}")
    }
    function calcAutoAssessmentLessonScore() {
        var dg = $("#tb-assessment-lesson-score-value").datagrid("getChecked")
        if (dg.length > 0) {
            $.post("{{ url('academic/assessment/lesson/score/final') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                method: "otomatis",
                weights: JSON.stringify(dg),
                exam_id: {{ $infos->id }},
                lesson_id: {{ $infos->lesson_id }},
                class_id: {{ $infos->class_id }},
                semester_id: {{ $infos->semester_id }},
                lesson_exam_id: {{ $infos->lesson_exam_id }},
                assessment_id: {{ $infos->lesson_assessment_id }},
            }, true), function(response) {
                $.messager.alert('Informasi', response.message)
                $("#tb-assessment-lesson-score").datagrid("reload")
                $("#tb-assessment-lesson-score-value").datagrid("reload")
            })            
        }
    }
    function exportAssessmentLessonScore() {
        var mainGrid = $("#tb-assessment-lesson-score").datagrid("getData").rows
        var weightGrid = $("#tb-assessment-lesson-score-value").datagrid("getChecked")
        $.messager.progress({ title: "Ekspor dokumen ke Excel", msg: "Mohon tunggu..." })
        $.post("{{ url('academic/assessment/lesson/export-excel') }}", $.param({ 
            _token: "{{ csrf_token() }}", 
            scores: JSON.stringify(mainGrid),
            assessment_id: "{{ $params[0] }}", 
            class_id: "{{ $params[1] }}", 
            semester_id: "{{ $params[2] }}",
            lesson: "{{ ucwords($infos->lesson) }}",
            aspect: "{{ ucwords($infos->score_aspect) }}",
            assessment: "{{ strtoupper($infos->assessment) }}",
            deptname: $("#assessmentLessonScoreDept").textbox("getValue"),
            semester: $("#assessmentLessonScoreSemester").textbox("getValue"),
            grade: $("#assessmentLessonScoreGrade").textbox("getValue"),
            teacher: $("#assessmentLessonScoreTeacher").textbox("getValue"),
            school_year: $("#assessmentLessonScoreSchoolYear").textbox("getValue"),
        }, true), function(response) {
            $.messager.progress("close")
            window.open("/storage/downloads/" + response)
        })            
    }
</script>