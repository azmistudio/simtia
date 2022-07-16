@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $WindowWidthleft = ($InnerWidth / 2 - 12) + 150 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $SubGridHeight = $InnerHeight - 401 . "px";
    $ThirdGridHeight = $InnerHeight - 318 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kenaikan Kelas</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-graduation-promote" class="panel-top">
            <a id="newGraduationPromote" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newGraduationPromote()">Baru</a>
            <a id="saveGraduationPromote" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveGraduationPromote()">Simpan</a>
            <a id="clearGraduationPromote" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearGraduationPromote()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-graduation-promote"></span>Kelas Tujuan: <span id="title-graduation-promote"></span></h6>
        </div>
        <div class="pt-3" id="page-graduation-promote">
            <form id="form-graduation-promote-main" method="post">
                <input type="hidden" id="id-graduation-promote" name="id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input name="department" id="GraduationPromoteDept" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year" id="GraduationPromoteSchoolyear" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationPromoteClassId" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas Awal:',
                                    labelWidth:'125px',
                                    panelWidth: 670,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:100},
                                        {field:'school_year',title:'Thn. Ajaran',width:100},
                                        {field:'grade',title:'Tingkat',width:100},
                                        {field:'class',title:'Kelas',width:170},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="grade" id="GraduationPromoteGrade" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <table id="tb-graduation-promote-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,resizeable:true,sortable:true">NIS</th>
                                            <th data-options="field:'name',width:225,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:80,resizeable:true,sortable:true">Kelas</th>
                                            <th data-options="field:'remark',width:200,editor:'text'">Keterangan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="mb-1">
                                <input name="school_year_dst" id="GraduationPromoteSchoolyearDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="grade_dst" id="GraduationPromoteGradeDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div>
                                <select name="class_id_dst" id="GraduationPromoteClassIdDest" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas Tujuan:',
                                    labelWidth:'125px',
                                    panelWidth: 670,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:100},
                                        {field:'schoolyear_id',title:'Thn. Ajaran',width:100},
                                        {field:'grade',title:'Tingkat',width:100},
                                        {field:'class',title:'Kelas',width:170},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="capacity" id="GraduationPromoteCapacity" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Kapasitas:',labelWidth:'125px',readonly:true" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="pt-3 pb-3">
            <form id="form-graduation-promoted-student" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="GraduationPromoteDeptDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationPromoteSchoolyearDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationPromoteGradeDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationPromoteClassIdDestination" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'Kelas:',
                                    labelWidth:'125px',
                                    panelWidth: 550,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:100},
                                        {field:'school_year',title:'Thn. Ajaran',width:100},
                                        {field:'grade',title:'Tingkat',width:100},
                                        {field:'class',title:'Kelas',width:170},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div>
                                <table id="tb-graduation-promoted-student" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarGraduationPromote">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,editor:'text'">NIS</th>
                                            <th data-options="field:'name',width:240,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:100,editor:'text'">Kelas</th>
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
<script type="text/javascript">
    var menuActionGraduationPromote = document.getElementById("menu-act-graduation-promote").getElementsByTagName("a")
    var markGraduationPromote = document.getElementById("mark-graduation-promote")
    var titleGraduationPromote = document.getElementById("title-graduation-promote")
    var idGraduationPromote = document.getElementById("id-graduation-promote")
    var menubarGraduationPromote = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-graduation-promoted-student').datagrid('getChecked')
            if (rows.length > 1) {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            } else {
                detailStudent(rows[0].id)
            }
        }
    },'-',{
        text: 'Hapus Terpilih',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            var rows = $('#tb-graduation-promoted-student').datagrid('getChecked')
            if (rows.length > 0) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kenaikan Kelas Santri terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/graduation/promote/destroy') }}", {class_id: $("#GraduationPromoteClassIdDestination").combobox('getValue'), students: rows, _token: '{{ csrf_token() }}'}, function(response) {
                            if (response.success) {
                                $.messager.alert('Informasi', response.message)
                                actionClearGraduationPromote()
                                $("#tb-graduation-promoted-student").datagrid("reload")
                                $("#GraduationPromoteClassId").combogrid("grid").datagrid("reload")
                                $("#GraduationPromoteClassIdDestination").combogrid("grid").datagrid("reload")
                                $("#form-graduation-promoted-student").form("reset")
                            } else {
                                $.messager.alert('Peringatan', response.message, 'error')
                            }
                        })
                    }
                })
            }
        }
    }]
    $(function () {
        sessionStorage.formKenaikan_Kelas = "init"
        actionButtonGraduationPromote("{{ $ViewType }}", [1,2])
        $("#GraduationPromoteClassId").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 2, fcount: 1 },
            onClickRow: function (index, row) {
                $("#GraduationPromoteDept").textbox('setValue', row.department)
                $("#GraduationPromoteSchoolyear").textbox('setValue', row.school_year)
                $("#GraduationPromoteGrade").textbox('setValue', row.grade)
                $("#GraduationPromoteClassId").combogrid('hidePanel')
                $("#tb-graduation-promote-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
                $("#GraduationPromoteClassIdDest").combogrid("grid").datagrid("load", "{{ url('academic/class/combo-grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass_not=" + row.id + "&fschoolyear_not=" + row.schoolyear_id + "&fgrade_not=" + row.grade_id)
            }
        })
        $("#tb-graduation-promote-student").datagrid("enableCellEditing").datagrid("enableFilter")
        $("#tb-graduation-promoted-student").datagrid("enableFilter")
        $("#GraduationPromoteClassIdDest").combogrid({
            onClickRow: function (index, row) {
                let capacities = row.capacity.split("/")
                if (parseInt(capacities[0]) == parseInt(capacities[1])) {
                    $.messager.alert('Peringatan', 'Kuota Kelas ' + row.class + ' sudah terpenuhi, silahkan pilih Kelas lainnya.', 'error')
                    $("#GraduationPromoteClassIdDest").combogrid('clear')    
                } else {
                    $("#GraduationPromoteSchoolyearDest").textbox('setValue', row.schoolyear_id)
                    $("#GraduationPromoteGradeDest").textbox('setValue', row.grade)
                    $("#GraduationPromoteCapacity").textbox('setValue', "Kuota: " + capacities[0] + ", Terisi: " + capacities[1])
                    titleGraduationPromote.innerText = row.class
                }
                $("#GraduationPromoteClassIdDest").combogrid('hidePanel')
            }
        })
        $("#GraduationPromoteClassIdDestination").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote', 
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 1, fcount: 1 },
            onClickRow: function (index, row) {
                $("#GraduationPromoteDeptDestination").textbox('setValue', row.department)
                $("#GraduationPromoteSchoolyearDestination").textbox('setValue', row.school_year)
                $("#GraduationPromoteGradeDestination").textbox('setValue', row.grade)
                $("#GraduationPromoteClassIdDestination").combogrid('hidePanel')
                $("#tb-graduation-promoted-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
            }
        })
        $("#page-graduation-promote").waitMe({effect:"none"})
    })
    function newGraduationPromote() {
        sessionStorage.formKenaikan_Kelas = "active"
        $("#form-graduation-promote-main").form("reset")
        actionButtonGraduationPromote("active", [0])
        markGraduationPromote.innerText = "*"
        titleGraduationPromote.innerText = ""
        idGraduationPromote.value = "-1"
        $("#tb-graduation-promote-student").datagrid("loadData", [])
        $("#page-graduation-promote").waitMe("hide")
    }
    function saveGraduationPromote() {
        if (sessionStorage.formKenaikan_Kelas == "active") {
            var dg = $("#tb-graduation-promote-student").datagrid('getChecked')
            if (dg.length > -1)
            {
                $("#form-graduation-promote-main").ajaxSubmit({
                    url: "{{ url('academic/graduation/promote/store') }}",
                    data: { _token: '{{ csrf_token() }}', students: dg },
                    beforeSubmit: function(formData, jqForm, options) {
                        $("#page-graduation-promote").waitMe({effect:"facebook"})
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({icon:"success",title:response.message})
                            actionClearGraduationPromote()
                            $("#GraduationPromoteClassIdDestination").combogrid("grid").datagrid("reload")
                        } else {
                            $.messager.alert('Peringatan', response.message, 'error')
                        }
                        $("#page-graduation-promote").waitMe("hide")
                    },
                    error: function(xhr) {
                        failResponse(xhr)
                        $("#page-graduation-promote").waitMe("hide")
                    }
                })
            }
            return false
        }
    }
    function clearGraduationPromote() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearGraduationPromote()
            }
        })
    }
    function actionButtonGraduationPromote(viewType, idxArray) {
        for (var i = 0; i < menuActionGraduationPromote.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionGraduationPromote[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionGraduationPromote[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionGraduationPromote[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionGraduationPromote[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearGraduationPromote() {
        sessionStorage.formKenaikan_Kelas = "init"
        $("#form-graduation-promote-main").form("reset")
        actionButtonGraduationPromote("init", [])
        titleGraduationPromote.innerText = ""
        markGraduationPromote.innerText = ""
        idGraduationPromote.value = "-1"
        $("#tb-graduation-promote-student").datagrid("loadData", [])
        $("#page-graduation-promote").waitMe({effect:"none"})
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
</script>