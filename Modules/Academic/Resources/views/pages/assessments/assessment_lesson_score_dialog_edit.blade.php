<div class="container-fluid">
	<div class="row">
        <div class="col-12">
        	<form id="form-assessment-lesson-score-edit" method="post" action="{{ url('academic/assessment/lesson/score/edit/store') }}">
                @csrf
        		<input type="hidden" name="student_id" value="{{ $requests['student_id'] }}" />
        		<div class="mb-1">
                    <input name="student_no" class="easyui-textbox" id="AssessmentLessonScoreEditStudentNo" style="width:335px;height:22px;" data-options="label:'NIS:',labelWidth:'175px',readonly:true" value="{{ $requests['student_no'] }}" />
                </div>
                <div class="mb-1">
                    <input name="student_name" class="easyui-textbox" id="AssessmentLessonScoreEditStudentName" style="width:335px;height:22px;" data-options="label:'Nama:',labelWidth:'175px',readonly:true" value="{{ $requests['student_name'] }}" />
                </div>
                @php $i = 1; @endphp
                @foreach ($scores as $score)
                <div class="mb-1">
                	<input type="hidden" name="score[]" value="{{ $score->id }}-{{ $score->score }}" />
                    <input name="new_score[]" class="easyui-numberbox" style="width:235px;height:22px;" data-options="label:'*Nilai {{ strtoupper($code) }}-{{ $i }}:',labelWidth:'175px',precision:2,min:0" value="{{ $score->score }}" />
                </div>
                @php $i++; @endphp
                @endforeach
                <div class="mb-1">
                    <input name="reason" class="easyui-textbox" id="AssessmentLessonScoreEditStudentReason" style="width:535px;height:22px;" data-options="label:'*Alasan Perubahan Nilai:',labelWidth:'175px'" />
                </div>
                <div class="mb-1">
                    <input name="remark" class="easyui-textbox" id="AssessmentLessonScoreEditStudentRemark" style="width:535px;height:22px;" data-options="label:'Keterangan:',labelWidth:'175px'" />
                </div>
                <div class="mb-3" style="margin-left:175px;">
                	<a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="saveAssessmentLessonScoreEdit()">Simpan</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#form-assessment-lesson-score-edit').form('reset');$('#assessment-lesson-score-edit-w').window('close')">Batal</a>
                </div>
                <div class="well">
                	<p class="mb-0"><b><i>Setelah merubah nilai ujian, disarankan untuk menghitung ulang nilai akhir santri.</i></b></p>
                </div>
        	</form>
        </div>
    </div>
</div>
<script type="text/javascript">
    function saveAssessmentLessonScoreEdit() {
        $("#form-assessment-lesson-score-edit").ajaxSubmit({
            success: function(response) {
                if (response.success) {
                    $.messager.alert('Informasi', response.message)
                    $("#assessment-lesson-score-edit-w").window("close")
                    $("#tb-assessment-lesson-score").datagrid("reload")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            },
            error: function(xhr) {
                failResponse(xhr)
            }
        })
        return false
    }
</script>