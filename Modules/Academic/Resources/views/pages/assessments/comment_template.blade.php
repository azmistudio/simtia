<form id="form-comment-template" method="post">
<input type="hidden" name="id" value="{{ $requests['id'] }}" />
<input type="hidden" name="lesson_id" value="{{ $requests['lesson_id'] }}" />
<input type="hidden" name="score_aspect_id" value="{{ $requests['score_aspect_id'] }}" />
<input type="hidden" name="grade_id" value="{{ $grade }}" />
<input type="hidden" name="type_id" value="{{ $type_id }}" />
<input type="hidden" name="type" value="{{ $type }}" />
<div class="container-fluid">
	<div class="row">
        <div class="col-12 p-2">
            <p><b>Aspek Penilaian: {{ $title }}</b></p>
            <div id="commentTemplate" class="easyui-texteditor" title="" style="width:450px;height:170px;padding:10px" data-options="name:'comment',toolbar:['bold','italic','strikethrough','underline','-','justifyleft','justifycenter','justifyright','justifyfull','-','insertorderedlist','insertunorderedlist','outdent','indent']"></div>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
    $(function () {
        $("#commentTemplate").texteditor("setValue", '{!! $templates->comment !!}')
    })
    function saveCommentTemplate() {
        $("#form-comment-template").ajaxSubmit({
            url: "{{ url('academic/assessment/report/comment/template/store') }}",
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if (response.success) {
                    $("#assessment-report-comment-template-w").window("close")
                    $.messager.alert('Informasi', response.message)
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            },
            error: function(xhr) {
                if (xhr.status == 422) {
                    $.messager.alert('Peringatan', 'Input yang bertanda bintang (*) wajib diisi.', 'error')
                } else if (xhr.status == 419) {
                    $.messager.alert('Peringatan', 'Sesi Anda telah berakhir, silahkan muat ulang (tekan tombol F5) untuk memulai lagi.', 'error')
                } else {
                    $.messager.alert('Peringatan', 'Terjadi gangguan pada Server, silahkan ulangi kembali.', 'error')
                }
            }
        })
        return false
    }
</script>