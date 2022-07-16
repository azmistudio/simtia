<form id="form-assessment-report-comment-social" method="post">
<input type="hidden" name="type" value="social" />
<input type="hidden" name="student_id" value="{{ $students['student_id'] }}" />
<input type="hidden" id="lesson-id" value="{{ $report->lesson_id }}" />
<input type="hidden" id="class-id" name="class_id" value="{{ $report->class_id }}" />
<input type="hidden" id="semester-id" name="semester_id" value="{{ $report->semester_id }}" />
<div class="container-fluid" id="page-report-comment-social">
	<div class="row">
        <div class="col-12 p-2">
            <div class="mb-1">
                <input value="{{ $students['student_no'] }}" class="easyui-textbox" id="assessmentReportCommentSocialStudentNo" style="width:300px;height:22px;" data-options="label:'NIS:',labelWidth:'120px',readonly:true" />
            </div>
            <div class="mb-3">
                <input value="{{ $students['student_name'] }}" class="easyui-textbox" id="assessmentReportCommentSocialStudentName" style="width:300px;height:22px;" data-options="label:'Nama Santri:',labelWidth:'120px',readonly:true" />
            </div>
        </div>
        <div class="col-6 p-2">
            <fieldset>
                <legend><b>Komentar Sikap Spiritual</b></legend>
                    <div class="mb-2">
                        <select name="type_id[]" id="type-spiritual" class="easyui-combobox" style="width:373px;height:22px;" data-options="label:'Predikat:',labelWidth:'130px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                            @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <select id="template-spiritual" class="easyui-combobox" style="width:373px;height:22px;" data-options="label:'Pilih dari template:',labelWidth:'130px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                            @php $i = 1; @endphp
                            @foreach ($templates as $template)
                                @if (
                                    // $template->lesson_id == $report->lesson_id && 
                                    // $template->grade_id == $grade && 
                                    $template->aspect == 'spiritual'
                                )
                                    <option value="{{ $template->id }}">{{ $i .'. '. substr(strip_tags(html_entity_decode($template->comment)), 0, 25) }}</option>
                                @endif
                                @php $i++; @endphp
                            @endforeach   
                        </select>
                        <span class="mr-1"></span>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="useCommentTemplateSocial('spiritual')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clicked'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="editCommentTemplateSocial('template-spiritual','Spiritual')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Edit'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="deleteCommentTemplateSocial('template-spiritual', {{ $grade }})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Delete'"></a>
                    </div>
                    <div id="comment-spiritual" class="easyui-texteditor" title="" style="width:100%;height:150px;padding:10px" data-options="name:'comment[]',toolbar:['bold','italic','strikethrough','underline','-','justifyleft','justifycenter','justifyright','justifyfull','-','insertorderedlist','insertunorderedlist','outdent','indent']"></div>
                    <div class="mt-2">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="addToTemplateSocial('comment-spiritual')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Save'">Simpan komentar sebagai template</a>
                    </div>
            </fieldset>
        </div>
        <div class="col-6 p-2">
            <fieldset>
                <legend><b>Komentar Sikap Sosial</b></legend>
                    <div class="mb-2">
                        <select name="type_id[]" id="type-social" class="easyui-combobox" style="width:373px;height:22px;" data-options="label:'Predikat:',labelWidth:'130px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                            @foreach ($types as $type)
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <select id="template-social" class="easyui-combobox" style="width:373px;height:22px;" data-options="label:'Pilih dari template:',labelWidth:'130px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'">
                            @php $i = 1; @endphp
                            @foreach ($templates as $template)
                                @if (
                                    // $template->lesson_id == $report->lesson_id && 
                                    // $template->grade_id == $grade && 
                                    $template->aspect == 'social'
                                )
                                    <option value="{{ $template->id }}">{{ $i .'. '. substr(strip_tags(html_entity_decode($template->comment)), 0, 25) }}</option>
                                @endif
                                @php $i++; @endphp
                            @endforeach   
                        </select>
                        <span class="mr-1"></span>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="useCommentTemplateSocial('social')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clicked'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="editCommentTemplateSocial('template-social','Sosial')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Edit'"></a>
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="deleteCommentTemplateSocial('template-social', {{ $grade }})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Delete'"></a>
                    </div>
                    <div id="comment-social" class="easyui-texteditor" title="" style="width:100%;height:150px;padding:10px" data-options="name:'comment[]',toolbar:['bold','italic','strikethrough','underline','-','justifyleft','justifycenter','justifyright','justifyfull','-','insertorderedlist','insertunorderedlist','outdent','indent']"></div>
                    <div class="mt-2">
                        <a href="javascript:void(0)" class="easyui-linkbutton" onclick="addToTemplateSocial('comment-social')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Save'">Simpan komentar sebagai template</a>
                    </div>
            </fieldset>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
    $(function () {
        $("#comment-spiritual").texteditor('setValue', '{!! isset($comments[0]->comment) ? html_entity_decode($comments[0]->comment) : '' !!}')
        $("#comment-social").texteditor('setValue', '{!! isset($comments[1]->comment) ? html_entity_decode($comments[1]->comment) : '' !!}')
        @if (isset($comments[0]->type_id))
        $("#type-spiritual").combobox("setValue", "{!! html_entity_decode($comments[0]->type_id) !!}")
        @endif
        @if (isset($comments[1]->type_id))
        $("#type-social").combobox("setValue", "{!! html_entity_decode($comments[1]->type_id) !!}")
        @endif
    })
    function useCommentTemplateSocial(id) {
        let template_id = $("#template-"+id).combobox('getValue')
        if (template_id !== "") {
            $.get("{{ url('academic/assessment/report/comment/template/combo-box') }}" + "/" + template_id, $.param({'type': 'social'}, true), function(response) {
                $("#comment-"+id).texteditor('setValue', response)
            })
        }
    }
    function editCommentTemplateSocial(id, remark) {
        let template_id = $("#"+id).combobox('getValue')
        var comments = id.split("-")
        if (template_id !== "") {
            $("#assessment-report-comment-template-w").window("open")
            $("#assessment-report-comment-template-w").window("refresh", "{{ url('academic/assessment/report/comment/template') }}" + "?id=" + template_id + "&lesson_id=" + $("#lesson-id").val() + "&aspect=" + comments[1] + "&class_id=" + $("#class-id").val() + "&remark=" + remark + "&score_aspect_id=0" + "&type_id=" + $("#type-"+comments[1]).combobox('getValue') + "&grade_id=" + {{ $grade }})
        }
    }
    function deleteCommentTemplateSocial(id, grade_id) {
        let template_id = $("#"+id).combobox('getValue')
        var comments = id.split("-")
        if (template_id !== "") {
            $.post("{{ url('academic/assessment/report/comment/template/destroy') }}", $.param({ 
                _token: "{{ csrf_token() }}",
                id: template_id,
                type: 'social',
            }, true), function(response) {
                if (response.success) {
                    $("#"+id).combobox("reload", "{{ url('academic/assessment/report/comment/template/combo-box') }}" + "?type=social" + "&lesson_id=" + $("#lesson-id").val() + "&aspect=" + comments[1] + "&class_id=" + $("#class-id").val() + "&type_id=" + $("#type-"+comments[1]).combobox('getValue') + "&grade_id=" + grade_id + "&_token=" + "{{ csrf_token() }}")
                    $("#"+id).combobox("clear")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            })
        }
    }
    function addToTemplateSocial(id) {
        var comments = id.split("-")
        var comment = $("#"+id).texteditor('getValue')
        if (comment !== "") {
            $.post("{{ url('academic/assessment/report/comment/template/store') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                id: -1,
                type: 'social',
                type_id: $("#type-"+comments[1]).combobox('getValue'),  
                lesson_id: $("#lesson-id").val(),  
                class_id: $("#class-id").val(),  
                grade_id: {{ $grade }},
                comment: comment,
                aspect: comments[1]
            }, true), function(response) {
                if (response.success) {
                    $("#template-"+comments[1]).combobox("reload", "{{ url('academic/assessment/report/comment/template/combo-box') }}" + "?type=social" + "&lesson_id=" + $("#lesson-id").val() + "&aspect=" + comments[1] + "&class_id=" + $("#class-id").val() + "&type_id=" + $("#type-"+comments[1]).combobox('getValue') + "&grade_id=" + {{ $grade }} + "&_token=" + "{{ csrf_token() }}")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
            })            
        }
    }
    function saveCommentSocial() {
        $("#form-assessment-report-comment-social").ajaxSubmit({
            url: "{{ url('academic/assessment/report/comment/store') }}",
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-report-comment-social").waitMe({effect:"facebook"})
            },
            success: function(response) {
                if (response.success) {
                    $("#assessment-report-comment-social-w").window("close")
                    Toast.fire({icon:"success",title:response.message})
                    $("#tb-assessment-report-comment").datagrid("reload")
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $("#page-report-comment-social").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-report-comment-social").waitMe("hide")
            }
        })
        return false
    }
</script>