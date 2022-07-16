@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
    $ContentHeight = $InnerHeight - 270 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Rencana Program Pembelajaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportLessonPlan('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-lesson-plan" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-lesson-plan" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-lesson-plan" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($depts as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fcode-lesson-plan" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Kode:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="fname-lesson-plan" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterLessonPlan({fdept: $('#fdept-lesson-plan').val(),fcode: $('#fcode-lesson-plan').val(),fname: $('#fname-lesson-plan').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-lesson-plan').form('reset');filterLessonPlan({})">Batal</a>
                </div>
            </form>
            <table id="tb-lesson-plan" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'grade_id',width:40,resizeable:true,sortable:true">Tingkat</th>
                        <th data-options="field:'semester_id',width:40,resizeable:true,sortable:true">Semester</th>
                        <th data-options="field:'code',width:90,resizeable:true,sortable:true">Kode</th>
                        <th data-options="field:'subject',width:170,resizeable:true,sortable:true">Materi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-lesson-plan" class="panel-top">
            <a id="newLessonPlan" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonPlan()">Baru</a>
            <a id="editLessonPlan" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonPlan()">Ubah</a>
            <a id="saveLessonPlan" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonPlan()">Simpan</a>
            <a id="clearLessonPlan" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonPlan()">Batal</a>
            <a id="deleteLessonPlan" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonPlan()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson-plan"></span>Pelajaran: <span id="title-lesson-plan"></span></h6>
        </div>
        <div id="page-lesson-plan" class="pt-3 pb-3">
            <form id="form-lesson-plan-main" method="post" enctype="multipart/form-data">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-4">
                            <input type="hidden" id="id-lesson-plan" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:308px;height:22px;" data-options="label:'Departemen:',labelWidth:'115px',readonly:true" />
                                    <input type="hidden" id="LessonPlanDeptId" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="LessonPlanDeptId" class="easyui-combobox" style="width:308px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'115px',labelPosition:'before',panelHeight:125">
                                        @foreach ($depts as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <select name="grade_id" id="LessonPlanGradeId" class="easyui-combobox" style="width:308px;height:22px;" data-options="label:'<b>*</b>Tingkat:',labelWidth:'115px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'text'">
                                    <option value="">---</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="semester_id" id="LessonPlanSemesterId" class="easyui-combobox" style="width:308px;height:22px;" data-options="label:'<b>*</b>Semester:',labelWidth:'115px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'text'">
                                    <option value="">---</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="lesson_id" id="LessonPlanLessonId" class="easyui-combobox" style="width:308px;height:22px;" data-options="label:'<b>*</b>Pelajaran:',labelWidth:'115px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'text'">
                                    <option value="">---</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="code" class="easyui-textbox" style="width:308px;height:22px;" data-options="label:'<b>*</b>Kode RPP:',labelWidth:'115px'" />
                            </div>
                            <div class="mb-1">
                                <input name="subject" class="easyui-textbox" style="width:308px;height:22px;" data-options="label:'<b>*</b>Materi:',labelWidth:'115px'" />
                            </div>
                            <div class="mb-3">
                                <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'115px',labelPosition:'before'" />
                            </div>
                            <div class="mb-1">
                                <input name="lesson_plan_file[]" class="easyui-filebox" data-options="label:'Lampiran #1:',labelWidth:'115px',labelPosition:'before',buttonText:'Pilih'" style="width:308px;height:22px;">
                            </div>
                            <div class="mb-1">
                                <input name="lesson_plan_file[]" class="easyui-filebox" data-options="label:'Lampiran #2:',labelWidth:'115px',labelPosition:'before',buttonText:'Pilih'" style="width:308px;height:22px;">
                            </div>
                            <div class="mb-3">
                                <input name="lesson_plan_file[]" class="easyui-filebox" data-options="label:'Lampiran #3:',labelWidth:'115px',labelPosition:'before',buttonText:'Pilih'" style="width:308px;height:22px;">
                            </div>
                            <div class="" style="width:308px;">
                                <fieldset>
                                    <legend>Daftar Lampiran:</legend>
                                    <ul id="LessonPlanAttachments" style="padding-left:20px;"></ul>
                                </fieldset>
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="easyui-texteditor" id="LessonPlanDescId" title="Deskripsi Program Pembelajaran" style="width:100%;height:{{ $ContentHeight }};padding:20px" data-options="name:'description',toolbar:['bold','italic','strikethrough','underline','-','justifyleft','justifycenter','justifyright','justifyfull','-','insertorderedlist','insertunorderedlist','outdent','indent']"></div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionLessonPlan = document.getElementById("menu-act-lesson-plan").getElementsByTagName("a")
    var titleLessonPlan = document.getElementById("title-lesson-plan")
    var markLessonPlan = document.getElementById("mark-lesson-plan")
    var idLessonPlan = document.getElementById("id-lesson-plan")
    var dgLessonPlan = $("#tb-lesson-plan")
    $(function () {
        sessionStorage.formRpp = "init"
        dgLessonPlan.datagrid({
            url: "{{ url('academic/lesson/plan/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRpp == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonPlan.innerText = row.lesson
                    actionButtonLessonPlan("active",[2,3])
                    $("#form-lesson-plan-main").form("load", "{{ url('academic/lesson/plan/show') }}" + "/" + row.id)
                    $("#page-lesson-plan").waitMe("hide")
                }
            }
        })
        dgLessonPlan.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLessonPlan.datagrid('getPager').pagination())
        actionButtonLessonPlan("{{ $ViewType }}", [])
        $("#LessonPlanLessonId").combobox({
            onSelect: function(record) {
                titleLessonPlan.innerText = record.text
            }
        })
        $("#LessonPlanDeptId").combobox({
            onSelect: function(record) {
                if (record.value != "") {
                    comboBoxs(record.value)
                } else {
                    $("#LessonPlanGradeId").combobox("clear")
                    $("#LessonPlanGradeId").combobox("loadData", [])
                    $("#LessonPlanSemesterId").combobox("clear")
                    $("#LessonPlanSemesterId").combobox("loadData", [])
                    $("#LessonPlanLessonId").combobox("clear")
                    $("#LessonPlanLessonId").combobox("loadData", [])
                }
            }
        })
        @if (auth()->user()->getDepartment->is_all != 1)
            comboBoxs($("#LessonPlanDeptId").val())
        @endif
        $("#form-lesson-plan-main").form({
            onLoadSuccess: function(data) {
                $("#LessonPlanDescId").texteditor('setValue', data.description)
                $("#LessonPlanAttachments").html("")
                for (let i = 0; i < data.files.length; i++) {
                    let name = data.files[i].name.length > 35 ? data.files[i].name.substring(0,30) + '...' : data.files[i].name
                    let path = "storage/uploads/"+data.files[i].source_name+"/"+data.files[i].name
                    $("#LessonPlanAttachments").append("<li><a href='"+path+"' target='_blank'>"+name+"</a></li>")
                }
            }
        })
        $("#page-lesson-plan").waitMe({effect:"none"})
    })
    function comboBoxs(id) {
        $("#LessonPlanGradeId").combobox("reload", "{{ url('academic/grade/combo-box') }}" +"/"+ id + "?_token=" + "{{ csrf_token() }}")
        $("#LessonPlanSemesterId").combobox("reload", "{{ url('academic/semester/combo-box') }}" +"/"+ id + "?_token=" + "{{ csrf_token() }}")
        $("#LessonPlanLessonId").combobox("reload", "{{ url('academic/lesson/combo-box') }}" +"/"+ id + "?_token=" + "{{ csrf_token() }}")
    }
    function filterLessonPlan(params) {
        if (Object.keys(params).length > 0) {
            dgLessonPlan.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgLessonPlan.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newLessonPlan() {
        sessionStorage.formRpp = "active"
        $("#form-lesson-plan-main").form("reset")
        actionButtonLessonPlan("active", [0,1,4])
        markLessonPlan.innerText = "*"
        titleLessonPlan.innerText = ""
        idLessonPlan.value = "-1"
        $("#LessonPlanDescId").texteditor('setValue','')
        $("#LessonPlanAttachments").html("")
        $("#page-lesson-plan").waitMe("hide")
    }
    function editLessonPlan() {
        sessionStorage.formRpp = "active"
        markLessonPlan.innerText = "*"
        actionButtonLessonPlan("active", [0,1,4])
    }
    function saveLessonPlan() {
        if (sessionStorage.formRpp == "active") {
            ajaxLessonPlan("academic/lesson/plan/store")
        }
    }
    function deleteLessonPlan() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data RPP terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/lesson/plan/destroy') }}" +"/"+idLessonPlan.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonPlanResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxLessonPlan(route) {
        $("#form-lesson-plan-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-lesson-plan").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxLessonPlanResponse(response)
                $("#page-lesson-plan").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-lesson-plan").waitMe("hide")
            }
        })
        return false
    }
    function ajaxLessonPlanResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLessonPlan()
            $("#tb-lesson-plan").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearLessonPlan() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonPlan()
            }
        })
    }
    function actionButtonLessonPlan(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonPlan.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonPlan[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonPlan[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonPlan[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonPlan[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearLessonPlan() {
        sessionStorage.formRpp = "init"
        $("#form-lesson-plan-main").form("reset")
        actionButtonLessonPlan("init", [])
        titleLessonPlan.innerText = ""
        markLessonPlan.innerText = ""
        idLessonPlan.value = "-1"
        $("#LessonPlanDescId").texteditor('setValue','')
        $("#LessonPlanAttachments").html("")
        $("#page-lesson-plan").waitMe({effect:"none"})
    }
    function exportLessonPlan(document) {
        var dg = $("#tb-lesson-plan").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/plan/export-') }}" + document,dg.rows,"Ekspor data RPP ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    
</script>