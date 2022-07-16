@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 327 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Data Pelajaran</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportLesson('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-lesson" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-lesson" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-lesson" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($depts as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fcode-lesson" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Kode:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="fname-lesson" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterLesson({fdept: $('#fdept-lesson').val(),fcode: $('#fcode-lesson').val(),fname: $('#fname-lesson').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-lesson').form('reset');filterLesson({})">Batal</a>
                </div>
            </form>
            <table id="tb-lesson" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'code',width:80,resizeable:true,sortable:true">Kode</th>
                        <th data-options="field:'name',width:180,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-lesson" class="panel-top">
            <a id="newLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLesson()">Baru</a>
            <a id="editLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLesson()">Ubah</a>
            <a id="saveLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLesson()">Simpan</a>
            <a id="clearLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLesson()">Batal</a>
            <a id="deleteLesson" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLesson()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson"></span>Nama: <span id="title-lesson"></span></h6>
        </div>
        <div id="page-lesson" class="pt-3 pb-3">
            <form id="form-lesson-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-lesson" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="LessonDeptId" class="easyui-combobox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                        @foreach ($depts as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="LessonName" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                                @if (auth()->user()->getDepartment->is_all == 1)
                                    <span class="mr-2"></span>
                                    <input name="is_all" id="LessonAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Departemen',labelWidth:'140px',labelPosition:'after'" />
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="code" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Singkatan:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <label class="textbox-label textbox-label-before" style="text-align: left; width: 125px; height: 22px; line-height: 22px;"><b>*</b>Sifat:</label>
                                <input name="mandatory" class="easyui-radiobutton" value="1" data-options="label:'Wajib',labelPosition:'after'" checked="checked" />
                                <input name="mandatory" class="easyui-radiobutton" value="2" data-options="label:'Tambahan',labelPosition:'after'" />
                            </div>
                            <div class="mb-1">
                                <select name="group_id" class="easyui-combobox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Kelompok:',labelWidth:'125px',labelPosition:'before',panelHeight:68">
                                    <option value="">---</option>
                                    @foreach ($groups as $group)
                                    <option value="{{ $group->id }}">{{ $group->group }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:335px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionLesson = document.getElementById("menu-act-lesson").getElementsByTagName("a")
    var titleLesson = document.getElementById("title-lesson")
    var markLesson = document.getElementById("mark-lesson")
    var idLesson = document.getElementById("id-lesson")
    var dgLesson = $("#tb-lesson")
    $(function () {
        sessionStorage.formData_Pelajaran = "init"
        dgLesson.datagrid({
            url: "{{ url('academic/lesson/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formData_Pelajaran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLesson.innerText = row.name
                    actionButtonLesson("active",[2,3])
                    $("#form-lesson-main").form("load", "{{ url('academic/lesson/show') }}" + "/" + row.id)
                    $("#LessonAll").checkbox("disable")
                    $("#page-lesson").waitMe("hide")
                }
            }
        })
        dgLesson.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLesson.datagrid('getPager').pagination())
        actionButtonLesson("{{ $ViewType }}", [])
        $("#LessonName").textbox("textbox").bind("keyup", function (e) {
            titleLesson.innerText = $(this).val()
        })
        $("#page-lesson").waitMe({effect:"none"})
    })
    function filterLesson(params) {
        if (Object.keys(params).length > 0) {
            dgLesson.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgLesson.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newLesson() {
        sessionStorage.formData_Pelajaran = "active"
        $("#form-lesson-main").form("reset")
        actionButtonLesson("active", [0,1,4])
        markLesson.innerText = "*"
        titleLesson.innerText = ""
        idLesson.value = "-1"
        $("#LessonAll").checkbox("enable")
        $("#page-lesson").waitMe("hide")
    }
    function editLesson() {
        sessionStorage.formData_Pelajaran = "active"
        markLesson.innerText = "*"
        actionButtonLesson("active", [0,1,4])
    }
    function saveLesson() {
        if (sessionStorage.formData_Pelajaran == "active") {
            ajaxLesson("academic/lesson/store")
        }
    }
    function deleteLesson() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Pelajaran terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/lesson/destroy') }}" +"/"+idLesson.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function ajaxLesson(route) {
        $("#form-lesson-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-lesson").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxLessonResponse(response)
                $("#page-lesson").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-lesson").waitMe("hide")
            }
        })
        return false
    }
    function ajaxLessonResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLesson()
            $("#tb-lesson").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearLesson() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLesson()
            }
        })
    }
    function actionButtonLesson(viewType, idxArray) {
        for (var i = 0; i < menuActionLesson.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLesson[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLesson[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLesson[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLesson[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearLesson() {
        sessionStorage.formData_Pelajaran = "init"
        $("#form-lesson-main").form("reset")
        actionButtonLesson("init", [])
        titleLesson.innerText = ""
        markLesson.innerText = ""
        idLesson.value = "-1"
        $("#page-lesson").waitMe({effect:"none"})
    }
    function exportLesson(document) {
        var dg = $("#tb-lesson").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/export-') }}" + document,dg.rows,"Ekspor data Pelajaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>