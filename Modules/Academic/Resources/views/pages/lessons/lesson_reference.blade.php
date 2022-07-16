@php
    $WindowHeight = $InnerHeight - 276 . "px";
    $WindowWidth = $InnerWidth - 231 . "px";
    $TabHeight = $InnerHeight - 169 . "px";
    $TabContentHeight = $InnerHeight - 228 . "px";
    $TabGridHeight = $InnerHeight - 272 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Referensi Pelajaran</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">
            <div id="tt-reference-acd" class="easyui-tabs" style="height:{{ $TabHeight }}" data-options="plain:true,narrow:true,tabPosition:'left',headerWidth:200">
                <div title="Aspek Penilaian" class="p-1">
                    <div class="container-fluid mb-2">
                        <div class="row">
                            <div class="col-12 p-0 text-right">
                                <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAcademicLessonRef('tb-ref-score-aspect','{{ url('academic/lesson/reference/score-aspect/export-') }}','pdf','Aspek Penilaian')">Ekspor PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="easyui-layout" style="height:{{ $TabContentHeight }};width:{{ $WindowWidth }};">
                        <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
                            <div class="p-1">
                                <table id="tb-ref-score-aspect" class="easyui-datagrid" style="width:100%;height:{{ $TabGridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'basis',width:100,resizeable:true,sortable:true">Kode</th>
                                            <th data-options="field:'remark',width:150,resizeable:true,sortable:false">Aspek</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div data-options="region:'center'">
                            <div id="menu-act-score-aspect" class="panel-top"> 
                                <a id="newScoreAspect" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newScoreAspect()">Baru</a>
                                <a id="editScoreAspect" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editScoreAspect()">Ubah</a>
                                <a id="saveScoreAspect" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveScoreAspect()">Simpan</a>
                                <a id="clearScoreAspect" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearScoreAspect()">Batal</a>
                                <a id="deleteScoreAspect" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteScoreAspect()">Hapus</a>
                            </div>
                            <div class="title">
                                <h6><span id="mark-score-aspect"></span>Kode Aspek Penilaian: <span id="title-score-aspect"></span></h6>
                            </div>
                            <div class="p-3" id="page-score-aspect-main">
                                <form id="form-score-aspect-main" method="post">
                                    <input type="hidden" id="id-score-aspect" name="id" value="-1" />
                                    <div class="mb-1">
                                        <input name="basis" id="ScoreAspectBasisId" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Kode:',labelWidth:'125px',labelPosition:'before'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="remark" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Aspek:',labelWidth:'125px',labelPosition:'before'" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div title="Kelompok Pelajaran" class="p-1">
                    <div class="container-fluid mb-2">
                        <div class="row">
                            <div class="col-12 p-0 text-right">
                                <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAcademicLessonRef('tb-lesson-group','{{ url('academic/lesson/reference/lesson-group/export-') }}','pdf','Kelompok Pelajaran')">Ekspor PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="easyui-layout" style="height:{{ $TabContentHeight }};width:{{ $WindowWidth }};">
                        <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
                            <div class="p-1">
                                <table id="tb-lesson-group" class="easyui-datagrid" style="width:100%;height:{{ $TabGridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'code',width:100,resizeable:true,sortable:true">Kode</th>
                                            <th data-options="field:'group',width:150,resizeable:true,sortable:true">Kelompok</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div data-options="region:'center'">
                            <div id="menu-act-lesson-group" class="panel-top"> 
                                <a id="newLessonGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newLessonGroup()">Baru</a>
                                <a id="editLessonGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editLessonGroup()">Ubah</a>
                                <a id="saveLessonGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveLessonGroup()">Simpan</a>
                                <a id="clearLessonGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearLessonGroup()">Batal</a>
                                <a id="deleteLessonGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteLessonGroup()">Hapus</a>
                            </div>
                            <div class="title">
                                <h6><span id="mark-lesson-group"></span>Kode: <span id="title-lesson-group"></span></h6>
                            </div>
                            <div class="p-3" id="page-lesson-group-main">
                                <form id="form-lesson-group-main" method="post">
                                    <input type="hidden" id="id-lesson-group" name="id" value="-1" />
                                    <div class="mb-1">
                                        <input name="code" id="codeId" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Kode:',labelWidth:'125px',labelPosition:'before'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="group" class="easyui-textbox" style="width:335px;height:50px;" data-options="label:'<b>*</b>Kelompok:',labelWidth:'125px',labelPosition:'before',multiline:true" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="order" class="easyui-numberspinner" style="width:335px;height:22px;" data-options="label:'<b>*</b>Urutan:',labelWidth:'125px',labelPosition:'before',min:1" />
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionScoreAspect= document.getElementById("menu-act-score-aspect").getElementsByTagName("a")
    var markScoreAspect = document.getElementById("mark-score-aspect")
    var titleScoreAspect = document.getElementById("title-score-aspect")
    var idScoreAspect = document.getElementById("id-score-aspect")
    //
    var menuActionLessonGroup = document.getElementById("menu-act-lesson-group").getElementsByTagName("a")
    var markLessonGroup = document.getElementById("mark-lesson-group")
    var titleLessonGroup = document.getElementById("title-lesson-group")
    var idLessonGroup = document.getElementById("id-lesson-group")
    $(function () {
        sessionStorage.formRef_Pelajaran_ScoreAspect = "init"
        var dgScoreAspect = $("#tb-ref-score-aspect")
        dgScoreAspect.datagrid({
            url: "{{ url('academic/lesson/reference/score-aspect/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRef_Pelajaran_ScoreAspect == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleScoreAspect.innerText = row.basis.toUpperCase()
                    actionButtonScoreAspect("active",[2,3])
                    $("#form-score-aspect-main").form("load", "{{ url('academic/lesson/reference/score-aspect/show') }}" + "/" + row.id)
                    $("#page-score-aspect-main").waitMe("hide")
                }
            }
        })
        dgScoreAspect.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        var pagerScoreAspect = dgScoreAspect.datagrid('getPager').pagination()
        pagerScoreAspect[0].children[0].style.width = "100%"
        pagerScoreAspect[0].children[1].style.width = "100%"
        pagerScoreAspect[0].children[1].style.margin = "0"
        pagerScoreAspect[0].children[1].style.textAlign = "center"
        actionButtonScoreAspect("{{ $ViewType }}", [])
        $("#ScoreAspectBasisId").textbox("textbox").bind("keyup", function (e) {
            titleScoreAspect.innerText = $(this).val()
        })
        $("#page-score-aspect-main").waitMe({effect:"none"})
        // 
        sessionStorage.formRef_Pelajaran_LessonGroup = "init"
        var dgLessonGroup = $("#tb-lesson-group")
        dgLessonGroup.datagrid({
            url: "{{ url('academic/lesson/reference/lesson-group/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRef_Pelajaran_LessonGroup == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleLessonGroup.innerText = row.code.toUpperCase()
                    actionButtonLessonGroup("active",[2,3])
                    $("#form-lesson-group-main").form("load", "{{ url('academic/lesson/reference/lesson-group/show') }}" + "/" + row.id)
                    $("#page-lesson-group-main").waitMe("hide")
                }
            }
        })
        dgLessonGroup.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        var pagerLessonGroup = dgLessonGroup.datagrid('getPager').pagination()
        pagerLessonGroup[0].children[0].style.width = "100%"
        pagerLessonGroup[0].children[1].style.width = "100%"
        pagerLessonGroup[0].children[1].style.margin = "0"
        pagerLessonGroup[0].children[1].style.textAlign = "center"
        actionButtonLessonGroup("{{ $ViewType }}", [])
        $("#codeId").textbox("textbox").bind("keyup", function (e) {
            titleLessonGroup.innerText = $(this).val()
        })
        $("#page-lesson-group-main").waitMe({effect:"none"})
    })
    // score-aspect
    function newScoreAspect() {
        sessionStorage.formRef_Pelajaran_ScoreAspect = "active"
        $("#form-score-aspect-main").form("clear")
        actionButtonScoreAspect("active", [0,1,4])
        markScoreAspect.innerText = "*"
        titleScoreAspect.innerText = ""
        idScoreAspect.value = "-1"
        $("#ScoreAspectBasisId").textbox().focus()
        $("#page-score-aspect-main").waitMe("hide")
    }
    function editScoreAspect() {
        sessionStorage.formRef_Pelajaran_ScoreAspect = "active"
        markScoreAspect.innerText = "*"
        actionButtonScoreAspect("active", [0, 1, 4])
    }
    function saveScoreAspect() {
        if (sessionStorage.formRef_Pelajaran_ScoreAspect == "active") {
            ajaxAcademicLessonRef("form-score-aspect-main", "tb-ref-score-aspect", "academic/lesson/reference/score-aspect/store", "score-aspect")
        }
    }
    function deleteScoreAspect() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Aspek Penilaian terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                destroyAcademicLessonRef("tb-ref-score-aspect", "academic/lesson/reference/score-aspect/destroy/"+idScoreAspect.value, "score-aspect")
            }
        })
    }
    function clearScoreAspect() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearScoreAspect()
            }
        })
    }
    function actionButtonScoreAspect(viewType, idxArray) {
        for (var i = 0; i < menuActionScoreAspect.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionScoreAspect[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionScoreAspect[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionScoreAspect[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionScoreAspect[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearScoreAspect() {
        sessionStorage.formRef_Pelajaran_ScoreAspect = "init"
        $("#form-score-aspect-main").form("clear")
        actionButtonScoreAspect("init", [])
        titleScoreAspect.innerText = ""
        markScoreAspect.innerText = ""
        idScoreAspect.value = "-1"
        $("#page-score-aspect-main").waitMe({effect:"none"})
    }
    //
    function newLessonGroup() {
        sessionStorage.formRef_Pelajaran_LessonGroup = "active"
        $("#form-lesson-group-main").form("clear")
        actionButtonLessonGroup("active", [0,1,4])
        markLessonGroup.innerText = "*"
        titleLessonGroup.innerText = ""
        idLessonGroup.value = "-1"
        $("#page-lesson-group-main").waitMe("hide")
    }
    function editLessonGroup() {
        sessionStorage.formRef_Pelajaran_LessonGroup = "active"
        markLessonGroup.innerText = "*"
        actionButtonLessonGroup("active", [0, 1, 4])
    }
    function saveLessonGroup() {
        if (sessionStorage.formRef_Pelajaran_LessonGroup == "active") {
            ajaxAcademicLessonRef("form-lesson-group-main", "tb-lesson-group", "academic/lesson/reference/lesson-group/store", "lesson-group")
        }
    }
    function deleteLessonGroup() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kelompok Pelajaran terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                destroyAcademicLessonRef("tb-lesson-group", "academic/lesson/reference/lesson-group/destroy/"+idLessonGroup.value, "lesson-group")
            }
        })
    }
    function clearLessonGroup() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearLessonGroup()
            }
        })
    }
    function actionClearLessonGroup() {
        sessionStorage.formRef_Pelajaran_LessonGroup = "init"
        $("#form-lesson-group-main").form("clear")
        actionButtonLessonGroup("init", [])
        titleLessonGroup.innerText = ""
        markLessonGroup.innerText = ""
        idLessonGroup.value = "-1"
        $("#page-lesson-group-main").waitMe({effect:"none"})
    }
    function actionButtonLessonGroup(viewType, idxArray) {
        for (var i = 0; i < menuActionLessonGroup.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionLessonGroup[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionLessonGroup[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionLessonGroup[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionLessonGroup[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    
    // common used
    function exportAcademicLessonRef(idGrid, route, document, title) {
        var dg = $("#"+idGrid).datagrid('getData')
        if (dg.total > 0) {
            exportDocument(route + document,dg.rows,"Ekspor data "+title+" ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function ajaxAcademicLessonRef(idForm, idGrid, route, subject) {
        $("#"+idForm).ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-"+subject+"-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAcademicLessonRefResponse(response, idGrid, subject)
                $("#page-"+subject+"-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-"+subject+"-main").waitMe("hide")
            }
        })
        return false
    }
    function destroyAcademicLessonRef(idGrid, url, subject) {
        $.post(url, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
            ajaxAcademicLessonRefResponse(response, idGrid, subject)
        }).fail(function(xhr) {
            failResponse(xhr)
        })
    }
    function ajaxAcademicLessonRefResponse(response, idGrid, subject) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            switch(subject) {
                case "lesson-group":
                    actionClearLessonGroup()
                    break;
                default:
                    actionClearScoreAspect()
            }
            $("#"+idGrid).datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
</script>