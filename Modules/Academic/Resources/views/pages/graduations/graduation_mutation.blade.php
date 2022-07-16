@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $WindowWidthleft = ($InnerWidth / 2 - 12) + 150 . "px";
    $SubGridHeight = $InnerHeight - 507 . "px";
    $ThirdGridHeight = $InnerHeight - 346 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kelulusan - Pindah Departemen</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-graduation-mutation" class="panel-top">
            <a id="newGraduationMutation" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newGraduationMutation()">Baru</a>
            <a id="saveGraduationMutation" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveGraduationMutation()">Simpan</a>
            <a id="clearGraduationMutation" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearGraduationMutation()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-graduation-mutation"></span>Departemen Tujuan: <span id="title-graduation-mutation"></span></h6>
        </div>
        <div class="pt-3" id="page-graduation-mutation">
            <form id="form-graduation-mutation-main" method="post">
                <input type="hidden" id="id-graduation-mutation" name="id" value="-1" />
                <input type="hidden" id="id-graduation-mutation-dept" name="department_id" value="-1" />
                <input type="hidden" id="id-graduation-mutation-dept-dest" name="department_id_dst" value="-1" />
                <input type="hidden" id="id-graduation-mutation-grade" name="grade_id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input name="department" id="GraduationMutationDept" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year" id="GraduationMutationSchoolyear" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationMutationClassId" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas Awal:',
                                    labelWidth:'125px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:80,align:'center'},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120,align:'center'},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="grade" id="GraduationMutationGrade" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <table id="tb-graduation-mutation-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,resizeable:true,sortable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:200,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:80,resizeable:true,align:'center'">Kelas</th>
                                            <th data-options="field:'student_num',width:105,align:'center',editor:'text'">*NIS Baru</th>
                                            <th data-options="field:'remark',width:180,editor:'text'">Keterangan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="mb-1">
                                <ul class="well" style="font-size:13px;">
                                    <li><strong>Klik pada kolom NIS Baru untuk mengisi Nomor Induk Santri secara manual, jika tidak diisi, maka sistem akan membuat NIS secara otomatis.</strong></li>
                                    <li>Kolom Keterangan dapat diisi manual.</li>
                                </ul>
                            </div>
                            <div class="mb-1">
                                <input name="dept_dst" id="GraduationMutationDeptDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year_dst" id="GraduationMutationSchoolyearDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input name="generation_dst" id="GraduationMutationGenerationDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Angkatan:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="grade_dst" id="GraduationMutationGradeDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div>
                                <select name="class_id_dst" id="GraduationMutationClassIdDest" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas Tujuan:',
                                    labelWidth:'125px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:80,align:'center'},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120,align:'center'},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="date" id="GraduationMutationDateDest" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal Kelulusan:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="pt-3 pb-3">
            <form id="form-graduation-mutation-student" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="GraduationMutationDeptDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationMutationGenerationDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Angkatan:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationMutationSchoolyearDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationMutationGradeDestination" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationMutationClassIdDestination" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
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
                                <table id="tb-graduation-mutationd-student" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarGraduationMutation">
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
    var menuActionGraduationMutation = document.getElementById("menu-act-graduation-mutation").getElementsByTagName("a")
    var markGraduationMutation = document.getElementById("mark-graduation-mutation")
    var titleGraduationMutation = document.getElementById("title-graduation-mutation")
    var idGraduationMutation = document.getElementById("id-graduation-mutation")
    var menubarGraduationMutation = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-graduation-mutationd-student').datagrid('getChecked')
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
            var rows = $('#tb-graduation-mutationd-student').datagrid('getChecked')
            if (rows.length > 0) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kelulusan Santri terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/graduation/mutation/destroy') }}", {class_id: $("#GraduationMutationClassIdDestination").combobox('getValue'), students: rows, _token: '{{ csrf_token() }}'}, function(response) {
                            if (response.success) {
                                $.messager.alert('Informasi', response.message)
                                actionClearGraduationMutation()
                                $("#tb-graduation-mutationd-student").datagrid('reload')
                                $("#GraduationMutationClassId").combogrid("grid").datagrid("reload")
                                $("#GraduationMutationClassIdDestination").combogrid("grid").datagrid("reload")
                                $("#form-graduation-mutation-student").form("reset")
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
        sessionStorage.formKelulusan_Pindah = "init"
        actionButtonGraduationMutation("{{ $ViewType }}", [1,2])
        $("#GraduationMutationClassId").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 2, fcount: 1 },
            onClickRow: function (index, row) {
                $("#id-graduation-mutation-dept").val(row.department_id)
                $("#GraduationMutationDept").textbox('setValue', row.department)
                $("#GraduationMutationSchoolyear").textbox('setValue', row.school_year)
                $("#GraduationMutationGrade").textbox('setValue', row.grade)
                $("#GraduationMutationClassId").combogrid('hidePanel')
                $("#tb-graduation-mutation-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
                $("#GraduationMutationClassIdDest").combogrid("grid").datagrid("load", "{{ url('academic/class/combo-grid/view') }}" + "?_token=" + "{{ csrf_token() }}" + "&fdept_not=" + row.department_id + "&fscy_not=" + row.schoolyear_id + "&fscy_active=" + 1 + "&fsemester=I&fgrade=1")
                var end_dates = row.end_date.split("-")
                let end_date = new Date(end_dates[0], parseInt(end_dates[1]) - 1, end_dates[2])
                $("#GraduationMutationDateDest").datebox().datebox('calendar').calendar({
                    validator: function(date){
                        var now = new Date()
                        return date >= end_date
                    }
                })
                $("#GraduationMutationDateDest").datebox("setValue", row.end_date)
            }
        })
        $("#tb-graduation-mutation-student").datagrid("enableCellEditing").datagrid("enableFilter")
        $("#tb-graduation-mutationd-student").datagrid("enableFilter")
        $("#GraduationMutationClassIdDest").combogrid({
            onClickRow: function (index, row) {
                let capacities = row.capacity.split("/")
                if (parseInt(capacities[0]) == parseInt(capacities[1])) {
                    $.messager.alert('Peringatan', 'Kuota Kelas ' + row.class + ' sudah terpenuhi, silahkan pilih Kelas lainnya.', 'error')
                    $("#GraduationMutationClassIdDest").combogrid('clear')    
                } else {
                    let years = row.school_year.split("/")
                    $("#id-graduation-mutation-dept-dest").val(row.department_id)
                    $("#id-graduation-mutation-grade").val(row.grade_id)
                    $("#GraduationMutationDeptDest").textbox('setValue', row.department)
                    $("#GraduationMutationGenerationDest").textbox('setValue', years[0])
                    $("#GraduationMutationSchoolyearDest").textbox('setValue', row.school_year)
                    $("#GraduationMutationGradeDest").textbox('setValue', row.grade)
                    $("#GraduationMutationCapacity").textbox('setValue', "Kuota: " + capacities[0] + ", Terisi: " + capacities[1])
                    titleGraduationMutation.innerText = row.department +" - Kelas "+ row.class
                }
                $("#GraduationMutationClassIdDest").combogrid('hidePanel')
            }
        })
        $("#GraduationMutationClassIdDestination").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 1, fcount: 1 },
            onClickRow: function (index, row) {
                let years = row.school_year.split("/")
                $("#GraduationMutationDeptDestination").textbox('setValue', row.department)
                $("#GraduationMutationGenerationDestination").textbox('setValue', years[0])
                $("#GraduationMutationSchoolyearDestination").textbox('setValue', row.school_year)
                $("#GraduationMutationGradeDestination").textbox('setValue', row.grade)
                $("#GraduationMutationClassIdDestination").combogrid('hidePanel')
                $("#tb-graduation-mutationd-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
            }
        })
        $("#page-graduation-mutation").waitMe({effect:"none"})
    })
    function newGraduationMutation() {
        sessionStorage.formKelulusan_Pindah = "active"
        $("#form-graduation-mutation-main").form("reset")
        actionButtonGraduationMutation("active", [0])
        markGraduationMutation.innerText = "*"
        titleGraduationMutation.innerText = ""
        idGraduationMutation.value = "-1"
        $("#tb-graduation-mutation-student").datagrid("loadData", [])
        $("#page-graduation-mutation").waitMe("hide")
    }
    function saveGraduationMutation() {
        if (sessionStorage.formKelulusan_Pindah == "active") {
            var dg = $("#tb-graduation-mutation-student").datagrid('getChecked')
            if (dg.length > -1)
            {
                $("#form-graduation-mutation-main").ajaxSubmit({
                    url: "{{ url('academic/graduation/mutation/store') }}",
                    data: { _token: '{{ csrf_token() }}', students: dg },
                    beforeSubmit: function(formData, jqForm, options) {
                        $("#page-graduation-mutation").waitMe({effect:"facebook"})
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({icon:"success",title:response.message})
                            actionClearGraduationMutation()
                            $("#GraduationMutationClassIdDestination").combogrid("grid").datagrid("reload")
                        } else {
                            $.messager.alert('Peringatan', response.message, 'error')
                        }
                        $("#page-graduation-mutation").waitMe("hide")
                    },
                    error: function(xhr) {
                        failResponse(xhr)
                        $("#page-graduation-mutation").waitMe("hide")
                    }
                })
            }
            return false
        }
    }
    function clearGraduationMutation() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearGraduationMutation()
            }
        })
    }
    function actionButtonGraduationMutation(viewType, idxArray) {
        for (var i = 0; i < menuActionGraduationMutation.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionGraduationMutation[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionGraduationMutation[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionGraduationMutation[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionGraduationMutation[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearGraduationMutation() {
        sessionStorage.formKelulusan_Pindah = "init"
        $("#form-graduation-mutation-main").form("reset")
        actionButtonGraduationMutation("init", [])
        titleGraduationMutation.innerText = ""
        markGraduationMutation.innerText = ""
        idGraduationMutation.value = "-1"
        $("#tb-graduation-mutation-student").datagrid("loadData", [])
        $("#page-graduation-mutation").waitMe({effect:"none"})
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
</script>