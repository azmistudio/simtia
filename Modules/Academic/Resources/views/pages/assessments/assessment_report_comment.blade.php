@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $SubGridHeight = $InnerHeight - 322 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Komentar Rapor</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'center'">
        <div class="title">
            <h6><span id="mark-assessment-report-comment"></span>Pelajaran: <span id="title-assessment-report-comment"></span></h6>
        </div>
        <div id="page-assessment-report-comment" class="pt-3 pb-3">
            <form id="form-assessment-report-comment-main" method="post">
                <div class="container-fluid">
                    <div class="row row-cols-auto">
                        <div class="col-12">
                            <input type="hidden" id="id-assessment-report-comment" name="id" value="-1" />
                            <input type="hidden" id="id-assessment-report-comment-semester" name="semester_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-comment-employee" name="employee_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-comment-exam" name="lesson_exam_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-comment-class" name="class_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-comment-grade" name="grade_id" value="-1" />
                            <input type="hidden" id="id-assessment-report-comment-lesson" name="lesson_id" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AssessmentReportCommentDept" style="width:380px;height:22px;" data-options="label:'Departemen:',labelWidth:'170px',readonly:true" />
                                <span class="mr-2"></span>
                                <input class="easyui-textbox" id="AssessmentReportCommentGrade" style="width:380px;height:22px;" data-options="label:'Tingkat:',labelWidth:'170px',readonly:true" />
                                <span class="mr-2"></span>
                                <input class="easyui-textbox" id="AssessmentReportCommentSchoolYear" style="width:380px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="AssessmentReportCommentSemester" style="width:380px;height:22px;" data-options="label:'Semester:',labelWidth:'170px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="class" class="easyui-textbox" id="AssessmentReportCommentClass" style="width:380px;height:22px;" data-options="label:'Kelas:',labelWidth:'170px',readonly:true" />
                                <span class="mr-2"></span>
                                <input class="easyui-textbox" id="AssessmentReportCommentTeacher" style="width:380px;height:22px;" data-options="label:'Guru:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select id="AssessmentReportCommentLesson" class="easyui-combogrid" style="width:380px;height:22px;" data-options="
                                    label:'<b>*</b>Pelajaran:',
                                    labelWidth:'170px',
                                    panelWidth: 852,
                                    idField: 'seq',
                                    textField: 'lesson',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'school_year',title:'Thn. Ajaran',width:120,align:'center'},
                                        {field:'semester',title:'Semester',width:100,align:'center'},
                                        {field:'class',title:'Kelas',width:100},
                                        {field:'lesson',title:'Pelajaran',width:150},
                                        {field:'employee',title:'Guru',width:200},
                                    ]],
                                ">
                                </select>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div>
                                <table id="tb-assessment-report-comment-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                    data-options="method:'post',toolbar:menubarAssessmentReportComment,rownumbers:'true',pagination:'true',singleSelect:true,pageSize:50,
                                        pageList:[10,25,50,75,100],remoteFilter:true,clientPaging:false">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'student_id',width:50,hidden:true">ID</th>
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
<div id="assessment-report-comment-lesson-w" class="easyui-window" title="Komentar Pelajaran" data-options="modal:true,footer:'#footer-lesson-w',closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--Add'" style="width:80%;height:480px;padding:10px;"></div>
<div id="assessment-report-comment-social-w" class="easyui-window" title="Komentar Spiritual & Sosial" data-options="modal:true,footer:'#footer-social-w',closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--Add'" style="width:80%;height:480px;padding:10px;"></div>
<div id="assessment-report-comment-view-w" class="easyui-window" title="Lihat Komentar" data-options="modal:true,closed:true,minimizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--View'" style="width:80%;height:480px;padding:10px;"></div>
<div id="footer-lesson-w" style="padding:5px;">
    <div class="text-right">
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Save'" href="javascript:void(0)" onclick="saveCommentLesson()" style="width:80px;height: 22px;">Simpan</a>
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Cancel'" href="javascript:void(0)" onclick="$('#assessment-report-comment-lesson-w').window('close')" style="width:80px;height: 22px;">Batal</a>
    </div>
</div>
<div id="footer-social-w" style="padding:5px;">
    <div class="text-right">
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Save'" href="javascript:void(0)" onclick="saveCommentSocial()" style="width:80px;height: 22px;">Simpan</a>
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Cancel'" href="javascript:void(0)" onclick="$('#assessment-report-comment-social-w').window('close')" style="width:80px;height: 22px;">Batal</a>
    </div>
</div>
<div id="assessment-report-comment-template-w" class="easyui-window" title="Template Komentar" data-options="modal:true,footer:'#footer-template-w',closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--Edit'" style="width:500px;height:320px;padding:10px;"></div>
<div id="footer-template-w" style="padding:5px;">
    <div class="text-right">
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Save'" href="javascript:void(0)" onclick="saveCommentTemplate()" style="width:80px;height: 22px;">Simpan</a>
        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Cancel'" href="javascript:void(0)" onclick="$('#assessment-report-comment-template-w').window('close')" style="width:80px;height: 22px;">Batal</a>
    </div>
</div>
<script type="text/javascript">
    var titleAssessmentReportComment = document.getElementById("title-assessment-report-comment")
    var sgAssessmentReportComment = $("#tb-assessment-report-comment-student")
    var menubarAssessmentReportComment = [{
        text: 'Komentar Pelajaran',
        iconCls: 'ms-Icon ms-Icon--Add',
        handler: function() {
            var row = sgAssessmentReportComment.datagrid('getSelected')
            if (row != null) {
                openWindowComment("assessment-report-comment-lesson-w","{{ url('academic/assessment/report/comment/lesson') }}",row)
            } else {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            }
        }
    },'-',{
        text: 'Komentar Spiritual & Sosial',
        iconCls: 'ms-Icon ms-Icon--Add',
        handler: function() {
            var row = sgAssessmentReportComment.datagrid('getSelected')
            if (row != null) {
                openWindowComment("assessment-report-comment-social-w","{{ url('academic/assessment/report/comment/social') }}",row)
            } else {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            }
        }
    },'-',{
        text: 'Lihat Komentar',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = sgAssessmentReportComment.datagrid('getData')
            if (rows.total > 0) {
                if ($("#AssessmentReportCommentLesson").combobox('getValue') !== '') {
                    $("#assessment-report-comment-view-w").window("open")
                    $('#assessment-report-comment-view-w').window('refresh', "{{ url('academic/assessment/report/comment/view') }}"
                        + "?class_id=" + $("#id-assessment-report-comment-class").val()
                        + "&semester_id=" + $("#id-assessment-report-comment-semester").val() 
                        + "&employee_id=" + $("#id-assessment-report-comment-employee").val()  
                        + "&lesson_id=" + $("#id-assessment-report-comment-lesson").val()
                        + "&grade_id=" + $("#id-assessment-report-comment-grade").val()
                    )
                }
            } else {
                $.messager.alert('Peringatan', 'Tidak ada data Santri yang tersedia.', 'warning')
            }
        }
    },'-',{
        text: 'Hapus Komentar',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            var row = sgAssessmentReportComment.datagrid('getSelected')
            if (row != null) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Komentar Rapor Santri terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/assessment/report/comment/destroy') }}", $.param({ 
                            _token: "{{ csrf_token() }}",
                            lesson_id: $("#id-assessment-report-comment-lesson").val(),
                            class_id: $("#id-assessment-report-comment-class").val(),
                            semester_id: $("#id-assessment-report-comment-semester").val(),
                            employee_id: $("#id-assessment-report-comment-employee").val(),
                            student_id: row.student_id,
                        }, true), function(response) {
                            if (response.success) {
                                $.messager.alert('Informasi', response.message)
                            } else {
                                $.messager.alert('Peringatan', response.message, 'error')
                            }
                        })
                    }
                })
            } else {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            }
        }
    }]
    $(function () {
        $("#AssessmentReportCommentLesson").combogrid('grid').datagrid({
            url: '{{ url('academic/assessment/report/formula/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                titleAssessmentReportComment.innerText = row.lesson
                $("#id-assessment-report-comment-employee").val(row.employee_id)
                $("#id-assessment-report-comment-class").val(row.class_id)
                $("#id-assessment-report-comment-semester").val(row.semester_id)
                $("#id-assessment-report-comment-grade").val(row.grade_id)
                $("#id-assessment-report-comment-lesson").val(row.lesson_id)
                $("#AssessmentReportCommentDept").textbox("setValue", row.department)
                $("#AssessmentReportCommentGrade").textbox("setValue", row.grade )
                $("#AssessmentReportCommentSchoolYear").textbox("setValue", row.school_year)
                $("#AssessmentReportCommentSemester").textbox("setValue", row.semester)
                $("#AssessmentReportCommentClass").textbox("setValue", row.class)
                $("#AssessmentReportCommentTeacher").textbox('setValue', row.employee)
                $("#AssessmentReportCommentLesson").combogrid('hidePanel')
                $("#tb-assessment-report-comment-student").datagrid("load", "{{ url('academic/assessment/report/formula/list') }}"
                    + "?_token=" + "{{ csrf_token() }}" 
                    + "&lesson_id=" + row.lesson_id
                    + "&class_id=" + row.class_id
                    + "&semester_id=" + row.semester_id
                    + "&employee_id=" + row.employee_id
                )
            }
        })
        $("#form-assessment-report-comment-main").form({
            onLoadSuccess: function(data) {
                titleAssessmentReportComment.innerText = $("#AssessmentReportCommentLesson").combobox('getText')
                $("#id-assessment-report-comment-semester").val(data.semester_id)
                $("#id-assessment-report-comment-employee").val(data.employee_id)
                $("#id-assessment-report-comment-lesson").val(data.lesson_id)      
                $("#AssessmentReportCommentLesson").combogrid("setValue", 111351)          
                $("#AssessmentReportCommentDept").textbox('setValue', data.department)
                $("#AssessmentReportCommentGrade").textbox('setValue', data.grade)
                $("#AssessmentReportCommentSchoolYear").textbox('setValue', data.school_year)
                $("#AssessmentReportCommentSemester").textbox('setValue', data.semester)
                $("#AssessmentReportCommentTeacher").textbox('setValue', data.teacher)
            }
        })
        $("#tb-assessment-report-comment-student").datagrid({
            url: "{{ url('academic/assessment/report/formula/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&lesson_id=" + 0 + "&class_id=" + 0 + "&semester_id=" + 0 + "&employee_id=" + 0,
            queryParams: { _token: "{{ csrf_token() }}" },
        }).datagrid("enableFilter")
    })
    function openWindowComment(id, url, row) {
        if ($("#AssessmentReportCommentLesson").combobox('getValue') !== '') {
            $("#"+id).window("open")
            $('#'+id).window('refresh', url 
                + "?class_id=" + $("#id-assessment-report-comment-class").val()
                + "&semester_id=" + $("#id-assessment-report-comment-semester").val() 
                + "&employee_id=" + $("#id-assessment-report-comment-employee").val()  
                + "&lesson_id=" + $("#id-assessment-report-comment-lesson").val()
                + "&grade_id=" + $("#id-assessment-report-comment-grade").val()
                + "&student_no=" + row.student_no 
                + "&student_name=" + row.name 
                + "&student_id=" + row.student_id 
            )
        }
    }
</script>