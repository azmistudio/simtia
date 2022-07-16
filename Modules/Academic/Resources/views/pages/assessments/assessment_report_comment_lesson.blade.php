<form id="form-assessment-report-comment-lesson" method="post">
<input type="hidden" name="type" value="lesson" />
<input type="hidden" name="student_id" value="{{ $students['student_id'] }}" />
<div class="container-fluid" id="page-report-comment-lesson">
    <div class="row">
        <div class="col-12 p-2">
            <div class="mb-1">
                <input value="{{ $students['student_no'] }}" class="easyui-textbox" id="assessmentReportCommentLessonStudentNo" style="width:300px;height:22px;" data-options="label:'NIS:',labelWidth:'120px',readonly:true" />
            </div>
            <div class="mb-3">
                <input value="{{ $students['student_name'] }}" class="easyui-textbox" id="assessmentReportCommentLessonStudentName" style="width:300px;height:22px;" data-options="label:'Nama Santri:',labelWidth:'120px',readonly:true" />
            </div>
        </div>
        @foreach ($reports as $report)
        <div class="col-6 p-2">
            <input type="hidden" name="final_id[]" value="{{ $report->final_id }}" />
            <fieldset>
                <legend><b>Komentar {{ $report->getScoreAspect->remark }}</b></legend>
                    <table class="table table-bordered table-sm">
                        <tbody>
                            <tr>
                                <td>Nilai Angka</td>
                                <td width="15%"><b>{{ number_format($report->value,2) }}</b></td>
                                <td>Nilai Huruf</td>
                                <td width="15%"><b>{{ $report->value_letter }}</b></td>
                                {{-- <td>Nilai KKM</td>
                                <td width="15%"><b>{{ $report->kkm }}</b></td> --}}
                            </tr>
                        </tbody>
                    </table>
                    <div class="mb-2">
                        <select id="template-{{ $report->final_id }}" class="easyui-combobox" style="width:373px;height:22px;" data-options="label:'Pilih dari template:',labelWidth:'130px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                            @php $i = 1; @endphp
                            @foreach ($templates as $template)
                                @if (
                                    // $template->lesson_id == $report->lesson_id && 
                                    $template->score_aspect_id == $report->score_aspect_id 
                                    // && $template->grade_id == $grade
                                )
                                    <option value="{{ $template->id }}">{{ $i .'. '. substr(strip_tags(html_entity_decode($template->comment)), 0, 25) }}</option>
                                @endif
                                @php $i++; @endphp
                            @endforeach
                        </select>
                        <span class="mr-1"></span>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="useCommentTemplateLesson({{ $report->final_id }})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clicked'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="editCommentTemplateLesson('template-{{ $report->final_id }}',{{ $report->lesson_id }},{{ $report->score_aspect_id }},{{ $report->class_id }},{{ $grade }})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Edit'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="deleteCommentTemplateLesson('template-{{ $report->final_id }}',{{ $report->lesson_id }},{{ $report->score_aspect_id }},{{ $report->class_id }},{{ $grade }})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Delete'"></a>
                    </div>
                    <div id="comment-{{ $report->final_id }}" class="easyui-texteditor" title="" style="width:100%;height:150px;padding:10px" data-options="name:'comment[]',toolbar:['bold','italic','strikethrough','underline','-','justifyleft','justifycenter','justifyright','justifyfull','-','insertorderedlist','insertunorderedlist','outdent','indent']"></div>
                    <div class="mt-2">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="addToTemplate('comment-{{ $report->final_id }}',{{ $report->lesson_id }},{{ $report->score_aspect_id }},{{ $report->class_id }},{{ $grade }})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Save'">Simpan komentar sebagai template</a>
                    </div>
            </fieldset>
        </div>
        @endforeach     
    </div>
</div>
</form>
<script type="text/javascript">
    $(function () {
        @foreach ($reports as $report)
            $("#comment-"+"{{ $report->final_id }}").texteditor('setValue', '{!! html_entity_decode($report->comment) !!}')
        @endforeach
    })
    function useCommentTemplateLesson(id) {
        let template_id = $("#template-"+id).combobox('getValue')
        if (template_id !== "") {
            $.get("{{ url('academic/assessment/report/comment/template/combo-box') }}" + "/" + template_id, $.param({'type': 'lesson'}, true), function(response) {
                $("#comment-"+id).texteditor('setValue', response)
            })
        }
    }
    function editCommentTemplateLesson(id, lesson_id, score_aspect_id, class_id, grade_id) {
        let template_id = $("#"+id).combobox('getValue')
        if (template_id !== "") {
            $("#assessment-report-comment-template-w").window("open")
            $("#assessment-report-comment-template-w").window("refresh", "{{ url('academic/assessment/report/comment/template') }}" + "?id=" + template_id + "&lesson_id=" + lesson_id + "&score_aspect_id=" + score_aspect_id + "&class_id=" + class_id + "&grade_id=" + grade_id)
        }
    }
    function deleteCommentTemplateLesson(id, lesson_id, score_aspect_id, class_id, grade_id) {
        let template_id = $("#"+id).combobox('getValue')
        if (template_id !== "") {
            $.post("{{ url('academic/assessment/report/comment/template/destroy') }}", $.param({ 
                _token: "{{ csrf_token() }}",
                id: template_id,
                type: 'lesson',
            }, true), function(response) {
                if (response.success) {
                    $("#"+id).combobox("reload", "{{ url('academic/assessment/report/comment/template/combo-box') }}" + "?type=lesson" + "&lesson_id=" + lesson_id + "&score_aspect_id=" + score_aspect_id + "&class_id=" + class_id + "&grade_id=" + grade_id + "&_token=" + "{{ csrf_token() }}")
                    $("#"+id).combobox("clear")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            })
        }
    }
    function addToTemplate(id, lesson_id, score_aspect_id, class_id, grade_id) {
        var finals = id.split("-")
        var comment = $("#"+id).texteditor('getValue')
        if (comment !== "") {
            $.post("{{ url('academic/assessment/report/comment/template/store') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                id: -1,
                type: 'lesson',
                lesson_id: lesson_id,  
                score_aspect_id: score_aspect_id,  
                class_id: class_id,
                grade_id: grade_id,
                comment: comment
            }, true), function(response) {
                if (response.success) {
                    $("#template-"+finals[1]).combobox("reload", "{{ url('academic/assessment/report/comment/template/combo-box') }}" + "?type=lesson" + "&lesson_id=" + lesson_id + "&score_aspect_id=" + score_aspect_id + "&class_id=" + class_id + "&grade_id=" + grade_id + "&_token=" + "{{ csrf_token() }}")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            })            
        }
    }
    function saveCommentLesson() {
        $("#form-assessment-report-comment-lesson").ajaxSubmit({
            url: "{{ url('academic/assessment/report/comment/store') }}",
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-report-comment-lesson").waitMe({effect:"facebook"})
            },
            success: function(response) {
                if (response.success) {
                    $("#assessment-report-comment-lesson-w").window("close")
                    Toast.fire({icon:"success",title:response.message})
                    $("#tb-assessment-report-comment").datagrid("reload")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $("#page-report-comment-lesson").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-report-comment-lesson").waitMe("hide")
            }
        })
        return false
    }
</script>