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
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Tidak Naik Kelas</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-graduation-unpromote" class="panel-top">
            <a id="newGraduationUnpromote" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newGraduationUnpromote()">Baru</a>
            <a id="saveGraduationUnpromote" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveGraduationUnpromote()">Simpan</a>
            <a id="clearGraduationUnpromote" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearGraduationUnpromote()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-graduation-unpromote"></span>Kelas Tujuan: <span id="title-graduation-unpromote"></span></h6>
        </div>
        <div class="pt-3" id="page-graduation-unpromote">
            <form id="form-graduation-unpromote-main" method="post">
                <input type="hidden" id="id-graduation-unpromote" name="id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input name="department" id="GraduationUnpromoteDept" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year" id="GraduationUnpromoteSchoolyear" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationUnpromoteClassId" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
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
                                <input name="grade" id="GraduationUnpromoteGrade" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <table id="tb-graduation-unpromote-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
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
                                <input name="school_year_dst" id="GraduationUnpromoteSchoolyearDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="grade_dst" id="GraduationUnpromoteGradeDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div>
                                <select name="class_id_dst" id="GraduationUnpromoteClassIdDest" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
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
                                <input name="capacity" id="GraduationUnpromoteCapacity" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Kapasitas:',labelWidth:'125px',readonly:true" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="pt-3 pb-3">
            <form id="form-graduation-unpromote-student" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="GraduationUnpromoteDeptDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationUnpromoteSchoolyearDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationUnpromoteGradeDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationUnpromoteClassIdDestination" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
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
                                <table id="tb-graduation-unpromoted-student" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarGraduationUnpromote">
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
    var menuActionGraduationUnpromote = document.getElementById("menu-act-graduation-unpromote").getElementsByTagName("a")
    var markGraduationUnpromote = document.getElementById("mark-graduation-unpromote")
    var titleGraduationUnpromote = document.getElementById("title-graduation-unpromote")
    var idGraduationUnpromote = document.getElementById("id-graduation-unpromote")
    var menubarGraduationUnpromote = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-graduation-unpromoted-student').datagrid('getChecked')
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
            var rows = $('#tb-graduation-unpromoted-student').datagrid('getChecked')
            if (rows.length > 0) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Santri Tidak Naik Kelas terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/graduation/unpromote/destroy') }}", {class_id: $("#GraduationUnpromoteClassIdDestination").combobox('getValue'), students: rows, _token: '{{ csrf_token() }}'}, function(response) {
                            if (response.success) {
                                $.messager.alert('Informasi', response.message)
                                actionClearGraduationUnpromote()
                                $("#tb-graduation-unpromoted-student").datagrid('reload')
                                $("#GraduationUnpromoteClassId").combogrid("grid").datagrid("reload")
                                $("#GraduationUnpromoteClassIdDestination").combogrid("grid").datagrid("reload")
                                $("#form-graduation-unpromote-student").form("reset")
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
        sessionStorage.formTidak_Naik = "init"
        actionButtonGraduationUnpromote("{{ $ViewType }}", [1,2])
        $("#GraduationUnpromoteClassId").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 2, fcount: 1 },
            onClickRow: function (index, row) {
                $("#GraduationUnpromoteDept").textbox('setValue', row.department)
                $("#GraduationUnpromoteSchoolyear").textbox('setValue', row.school_year)
                $("#GraduationUnpromoteGrade").textbox('setValue', row.grade)
                $("#GraduationUnpromoteClassId").combogrid('hidePanel')
                $("#tb-graduation-unpromote-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
                $("#GraduationUnpromoteClassIdDest").combogrid("grid").datagrid("load", "{{ url('academic/class/combo-grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass_yes=" + row.id + "&fschoolyear_yes=" + row.schoolyear_id + "&fgrade_yes=" + row.grade_id)
            }
        })
        $("#tb-graduation-unpromote-student").datagrid("enableCellEditing").datagrid("enableFilter")
        $("#tb-graduation-unpromoted-student").datagrid("enableFilter")
        $("#GraduationUnpromoteClassIdDest").combogrid({
            onClickRow: function (index, row) {
                let capacities = row.capacity.split("/")
                if (parseInt(capacities[0]) == parseInt(capacities[1])) {
                    $.messager.alert('Peringatan', 'Kuota Kelas ' + row.class + ' sudah terpenuhi, silahkan pilih Kelas lainnya.', 'error')
                    $("#GraduationUnpromoteClassIdDest").combogrid('clear')    
                } else {
                    $("#GraduationUnpromoteSchoolyearDest").textbox('setValue', row.schoolyear_id)
                    $("#GraduationUnpromoteGradeDest").textbox('setValue', row.grade)
                    $("#GraduationUnpromoteCapacity").textbox('setValue', "Kuota: " + capacities[0] + ", Terisi: " + capacities[1])
                    titleGraduationUnpromote.innerText = row.class
                }
                $("#GraduationUnpromoteClassIdDest").combogrid('hidePanel')
            }
        })
        $("#GraduationUnpromoteClassIdDestination").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 1, fcount: 1 },
            onClickRow: function (index, row) {
                $("#GraduationUnpromoteDeptDestination").textbox('setValue', row.department)
                $("#GraduationUnpromoteSchoolyearDestination").textbox('setValue', row.school_year)
                $("#GraduationUnpromoteGradeDestination").textbox('setValue', row.grade)
                $("#GraduationUnpromoteClassIdDestination").combogrid('hidePanel')
                $("#tb-graduation-unpromoted-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
            }
        })
        $("#page-graduation-unpromote").waitMe({effect:"none"})
    })
    function newGraduationUnpromote() {
        sessionStorage.formTidak_Naik = "active"
        $("#form-graduation-unpromote-main").form("reset")
        actionButtonGraduationUnpromote("active", [0])
        markGraduationUnpromote.innerText = "*"
        titleGraduationUnpromote.innerText = ""
        idGraduationUnpromote.value = "-1"
        $("#tb-graduation-unpromote-student").datagrid("loadData", [])
        $("#page-graduation-unpromote").waitMe("hide")
    }
    function saveGraduationUnpromote() {
        if (sessionStorage.formTidak_Naik == "active") {
            var dg = $("#tb-graduation-unpromote-student").datagrid('getChecked')
            if (dg.length > -1)
            {
                $("#form-graduation-unpromote-main").ajaxSubmit({
                    url: "{{ url('academic/graduation/unpromote/store') }}",
                    data: { _token: '{{ csrf_token() }}', students: dg },
                    beforeSubmit: function(formData, jqForm, options) {
                        $("#page-graduation-unpromote").waitMe({effect:"facebook"})
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({icon:"success",title:response.message})
                            actionClearGraduationUnpromote()
                            $("#GraduationUnpromoteClassIdDestination").combogrid("grid").datagrid("reload")
                        } else {
                            $.messager.alert('Peringatan', response.message, 'error')
                        }
                        $("#page-graduation-unpromote").waitMe("hide")
                    },
                    error: function(xhr) {
                        failResponse(xhr)
                        $("#page-graduation-unpromote").waitMe("hide")
                    }
                })
            }
            return false
        }
    }
    function clearGraduationUnpromote() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearGraduationUnpromote()
            }
        })
    }
    function actionButtonGraduationUnpromote(viewType, idxArray) {
        for (var i = 0; i < menuActionGraduationUnpromote.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionGraduationUnpromote[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionGraduationUnpromote[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionGraduationUnpromote[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionGraduationUnpromote[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearGraduationUnpromote() {
        sessionStorage.formTidak_Naik = "init"
        $("#form-graduation-unpromote-main").form("reset")
        actionButtonGraduationUnpromote("init", [])
        titleGraduationUnpromote.innerText = ""
        markGraduationUnpromote.innerText = ""
        idGraduationUnpromote.value = "-1"
        $("#tb-graduation-unpromote-student").datagrid("loadData", [])
        $("#page-graduation-unpromote").waitMe({effect:"none"})
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
</script>