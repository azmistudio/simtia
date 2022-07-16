@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 214 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Jam Belajar</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportLessonTime('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div style="padding:5px;">
            <table id="tb-lesson-time" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        @if (auth()->user()->getDepartment->is_all == 1)
                        <th data-options="field:'department_id',width:100,resizeable:true,sortable:true">Departemen</th>
                        @endif
                        <th data-options="field:'time',width:70,resizeable:true,sortable:true">Jam Ke</th>
                        <th data-options="field:'times',width:90,resizeable:true,sortable:true">Waktu</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-lesson-time" class="panel-top">
            <a id="newLessonTime" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonTime()">Baru</a>
            <a id="editLessonTime" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonTime()">Ubah</a>
            <a id="saveLessonTime" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonTime()">Simpan</a>
            <a id="clearLessonTime" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonTime()">Batal</a>
            <a id="deleteLessonTime" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonTime()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-lesson-time"></span>Jam Belajar ke: <span id="title-lesson-time"></span></h6>
        </div>
        <div id="page-lesson-time" class="pt-3 pb-3">
            <form id="form-lesson-time-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-lesson-time" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="LessonTimeDeptId" class="easyui-combobox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                        @foreach ($depts as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="time" id="LessonTimeNo" class="easyui-numberspinner" style="width:200px;height:22px;" data-options="label:'<b>*</b>Jam Ke:',labelWidth:'125px',min:1" />
                            </div>
                            <div class="mb-1">
                                <input name="start" class="easyui-timespinner" style="width:200px;height:22px;" data-options="label:'<b>*</b>Mulai:',labelWidth:'125px'" />
                                <span class="mr-2"></span>
                            </div>
                            <div class="mb-1">
                                <input name="end" class="easyui-timespinner" style="width:200px;height:22px;" data-options="label:'<b>*</b>Selesai:',labelWidth:'125px'" />
                                <span class="mr-2"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionLessonTime = document.getElementById("menu-act-lesson-time").getElementsByTagName("a")
    var titleLessonTime = document.getElementById("title-lesson-time")
    var markLessonTime = document.getElementById("mark-lesson-time")
    var idLessonTime = document.getElementById("id-lesson-time")
    var dgLessonTime = $("#tb-lesson-time")
    $(function () {
        sessionStorage.formJam_Belajar = "init"
        dgLessonTime.datagrid({
            url: "{{ url('academic/lesson/schedule/time/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formJam_Belajar == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonTime.innerText = row.time
                    actionButtonLessonTime("active",[2,3])
                    $("#form-lesson-time-main").form("load", "{{ url('academic/lesson/schedule/time/show') }}" + "/" + row.id)
                    $("#page-lesson-time").waitMe("hide")
                }
            }
        })
        dgLessonTime.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgLessonTime.datagrid('getPager').pagination())
        actionButtonLessonTime("{{ $ViewType }}", [])
        $("#LessonTimeNo").numberspinner({
            onSpinUp: function() {
                titleLessonTime.innerText = $(this).textbox('getValue')
            },
            onSpinDown: function() {
                titleLessonTime.innerText = $(this).textbox('getValue')
            }
        })
        $("#page-lesson-time").waitMe({effect:"none"})
    })
    function newLessonTime() {
        sessionStorage.formJam_Belajar = "active"
        $("#form-lesson-time-main").form("reset")
        actionButtonLessonTime("active", [0,1,4])
        markLessonTime.innerText = "*"
        titleLessonTime.innerText = ""
        idLessonTime.value = "-1"
        $("#page-lesson-time").waitMe("hide")
    }
    function editLessonTime() {
        sessionStorage.formJam_Belajar = "active"
        markLessonTime.innerText = "*"
        actionButtonLessonTime("active", [0,1,4])
    }
    function saveLessonTime() {
        if (sessionStorage.formJam_Belajar == "active") {
            ajaxLessonTime("academic/lesson/schedule/time/store")
        }
    }
    function deleteLessonTime() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Jam Belajar terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/lesson/schedule/time/destroy') }}" +"/"+idLessonTime.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxLessonTimeResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxLessonTime(route) {
        $("#form-lesson-time-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-lesson-time").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxLessonTimeResponse(response)
                $("#page-lesson-time").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-lesson-time").waitMe("hide")
            }
        })
        return false
    }
    function ajaxLessonTimeResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearLessonTime()
            $("#tb-lesson-time").datagrid("reload")
        } else {
            showError(response)
        }
    }
    function clearLessonTime() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonTime()
            }
        })
    }
    function actionButtonLessonTime(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonTime.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonTime[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonTime[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonTime[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonTime[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearLessonTime() {
        sessionStorage.formJam_Belajar = "init"
        $("#form-lesson-time-main").form("reset")
        actionButtonLessonTime("init", [])
        titleLessonTime.innerText = ""
        markLessonTime.innerText = ""
        idLessonTime.value = "-1"
        $("#page-lesson-time").waitMe({effect:"none"})
    }
    function exportLessonTime(document) {
        var dg = $("#tb-lesson-time").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/lesson/schedule/time/export-') }}" + document,dg.rows,"Ekspor data Jam Belajar ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>