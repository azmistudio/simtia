@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $WindowWidthleft = ($InnerWidth / 2 - 12) + 150 . "px";
    $SubGridHeight = $InnerHeight - 458 . "px";
    $ThirdGridHeight = $InnerHeight - 298 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Penempatan Calon Santri</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-placement" class="panel-top">
            <a id="newPlacement" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newPlacement()">Baru</a>
            <a id="savePlacement" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="savePlacement()">Simpan</a>
            <a id="clearPlacement" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearPlacement()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-placement"></span>Kelas Calon Santri: <span id="title-placement"></span></h6>
        </div>
        <div class="pt-3" id="page-placement-main">
            <form id="form-placement-main" method="post">
                <input type="hidden" id="id-placement" name="id" value="-1" />
                <input type="hidden" id="group-dept" value="" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="PlacementDept" class="easyui-textbox" style="width:275px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="PlacementProcess" class="easyui-textbox" style="width:225px;height:22px;" data-options="label:'Proses:',labelWidth:'75px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="PlacementProcessQuota" class="easyui-textbox" style="width:180px;height:22px;" data-options="label:'Kapasitas/Terisi:',labelWidth:'110px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="PlacementSchoolYear" class="easyui-textbox" style="width:275px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="PlacementGrade" class="easyui-textbox" style="width:225px;height:22px;" data-options="label:'Tingkat:',labelWidth:'75px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="PlacementQuota" class="easyui-textbox" style="width:180px;height:22px;" data-options="label:'Kapasitas/Terisi:',labelWidth:'110px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="prospect_group_id" id="PlacementProspectiveGroupId" class="easyui-combogrid" style="width:275px;height:22px;" data-options="
                                    label:'<b>*</b>Kelompok:',
                                    labelWidth:'125px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'group',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:120},
                                        {field:'admission_id',title:'Proses',width:200},
                                        {field:'group',title:'Kelompok',width:110},
                                        {field:'quota',title:'Kapasitas/Terisi',width:100},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <select name="class_id" id="PlacementClassId" class="easyui-combogrid" style="width:225px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'75px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    pagination:true,
                                    columns: [[
                                        {field:'schoolyear_id',title:'Thn. Ajaran',width:100},
                                        {field:'grade_id',title:'Tingkat',width:100},
                                        {field:'class',title:'Kelas',width:170},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <a href="javascript:void(0)" class="easyui-linkbutton" onclick="getProspectStudentDialog()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Database'">Ambil Data Calon Santri</a>
                            </div>
                            <div class="mb-1">
                                <table id="tb-placement-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'registration_no',width:120,resizeable:true,sortable:true">No. Pendaftaran</th>
                                            <th data-options="field:'name',width:225,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'student_no',width:100,editor:'text'">NIS</th>
                                            <th data-options="field:'remark',width:200,editor:'text'">Keterangan</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div>
                                <ul class="well" style="font-size:13px;">
                                    <li><strong>Klik pada kolom NIS untuk mengisi Nomor Induk Santri secara manual, jika tidak diisi, maka sistem akan membuat NIS secara otomatis.</strong></li>
                                    <li>Kolom Keterangan dapat diisi manual.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="pt-3">
            <form id="form-student" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="PlacementStudentDept" class="easyui-textbox" style="width:260px;height:22px;" data-options="label:'Departemen:',labelWidth:'100px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="PlacementStudentProcess" class="easyui-textbox" style="width:230px;height:22px;" data-options="label:'Proses:',labelWidth:'75px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="PlacementStudentSchoolYear" class="easyui-textbox" style="width:260px;height:22px;" data-options="label:'Thn. Ajaran:',labelWidth:'100px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="PlacementStudentGrade" class="easyui-textbox" style="width:230px;height:22px;" data-options="label:'Tingkat:',labelWidth:'75px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="PlacementStudentClassId" class="easyui-combogrid" style="width:260px;height:22px;" data-options="
                                    label:'Kelas:',
                                    labelWidth:'100px',
                                    panelWidth: 570,
                                    idField: 'seq',
                                    textField: 'class',
                                    fitColumns:true,
                                    pagination:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:120},
                                        {field:'admission',title:'Proses',width:250},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:120},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input id="PlacementStudentGroup" class="easyui-textbox" style="width:230px;height:22px;" data-options="label:'Kelompok:',labelWidth:'75px',readonly:true" />
                            </div>
                            <div>
                                <table id="tb-student-placement" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarPlacement,pagination:'true',pageSize:50,pageList:[10,25,50,75,100],remoteFilter:true,clientPaging:false">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'name',width:240,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'student_no',width:100,editor:'text'">NIS</th>
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
<div id="place-prospect-student-w" class="easyui-window" title="Daftar Calon Santri" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:800px;height:500px;padding:10px;">
    <div class="mb-1">
        <input class="easyui-textbox" id="prospectDeptId" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-1">
        <input class="easyui-textbox" id="prospectAdmissionId" style="width:335px;height:22px;" data-options="label:'Proses:',labelWidth:'125px',readonly:true" />
    </div>
    <div class="mb-3">
        <input class="easyui-textbox" id="prospectGroupId" style="width:335px;height:22px;" data-options="label:'Kelompok:',labelWidth:'125px',readonly:true" />
    </div>
    <div>
        <div class="table-filter">
            <form id="fg-prospect-student" method="post">
            @csrf
            <table>
                <tbody>
                    <tr>
                        <td style="width:30px;"></td>
                        <td style="width:28px;"></td>
                        <td style="width:150px;">
                            <input class="easyui-textbox" id="fg-prospect-student-reg" style="width:145px;height:22px;" />
                        </td>
                        <td style="width:100px;"></td>
                        <td style="width:120px;">
                            <select id="fg-gender" class="easyui-combobox" style="width:115px;height:22px;" data-options="panelHeight:125">
                                <option value="1">Laki-Laki</option>
                                <option value="2">Perempuan</option>
                            </select>
                        </td>
                        <td style="width:240px;">
                            <input class="easyui-textbox" id="fg-prospect-student-name" style="width:235px;height:22px;" />
                        </td>
                        <td style="width:100px;border-right: 0;text-align: left;">
                           <a href="javascript:void(0)" class="easyui-linkbutton" onclick="filterProspectStudent({fregister: $('#fg-prospect-student-reg').val(), fnisn: $('#fg-prospect-student-nisn').val(), fgender: $('#fg-gender').combobox('getValue'), fname: $('#fg-prospect-student-name').val()})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                           <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#fg-prospect-student').form('reset');filterProspectStudent({});" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        <table id="tb-place-prospective-student" class="easyui-datagrid" style="width:100%;height:320px" 
            data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100],toolbar:toolbarPlacement">
            <thead>
                <tr>
                    <th data-options="field:'ck',checkbox:true"></th>
                    <th data-options="field:'registration_no',width:150,resizeable:true,sortable:true">No. Pendaftaran</th>
                    <th data-options="field:'group',width:100,resizeable:true,align:'center'">Kelompok</th>
                    <th data-options="field:'gender',width:120,resizeable:true,align:'center'">Jenis Kelamin</th>
                    <th data-options="field:'name',width:235,resizeable:true,sortable:true">Nama</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var menuActionPlacement = document.getElementById("menu-act-placement").getElementsByTagName("a")
    var markPlacement = document.getElementById("mark-placement")
    var titlePlacement = document.getElementById("title-placement")
    var idPlacement = document.getElementById("id-placement")
    var toolbarPlacement = [{
        text: 'Ambil Terpilih',
        iconCls: 'ms-Icon ms-Icon--SelectAll',
        handler: function() {
            var rows = $('#tb-place-prospective-student').datagrid('getSelections')
            $("#tb-placement-student").datagrid({
                data: rows
            })
            $('#place-prospect-student-w').window('close')
        }
    }]
    var menubarPlacement = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-student-placement').datagrid('getSelections')
            if (rows.length > 1) {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            } else {
                detailStudent(rows[0].id.toString())
            }
        }
    },'-',{
        text: 'Hapus Terpilih',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            var rows = $('#tb-student-placement').datagrid('getSelections')
            $.messager.confirm("Konfirmasi", "Anda akan menghapus data Penempatan Santri terpilih, tetap lanjutkan?", function (r) {
                if (r) {
                    $.post("{{ url('academic/admission/placement/destroy') }}", {data: rows, _token: '{{ csrf_token() }}'}, function(response) {
                        if (response.success) {
                            $.messager.alert('Informasi', response.message)
                            actionClearPlacement()
                            $("#PlacementClassId").combogrid("grid").datagrid("reload")
                            $("#PlacementStudentClassId").combogrid("grid").datagrid("reload")
                            $("#tb-student-placement").datagrid("loadData", [])
                            $("#tb-placement-student").datagrid("loadData", [])
                            $("#form-student").form("reset")
                        } else {
                            $.messager.alert('Peringatan', response.message, 'error')
                        }
                    })
                }
            })
        }
    }]
    $(function () {
        sessionStorage.formPSB_Penempatan = "init"
        actionButtonPlacement("{{ $ViewType }}", [])
        $("#PlacementProspectiveGroupId").combogrid({
            url: '{{ url('academic/admission/prospective-group/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#PlacementClassId").combogrid("setValue", "")
                $("#PlacementDept").textbox("setValue", row.department)
                $("#PlacementProcess").textbox("setValue", row.admission_id)
                $("#PlacementProcessQuota").textbox("setValue", row.quota)
                $("#PlacementClassId").combogrid("grid").datagrid("reload", "{{ url('academic/class/placement/combo-grid') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + row.department_id)
                $("#page-placement-main").waitMe("hide")
                $("#PlacementProspectiveGroupId").combogrid('hidePanel')
            }
        })
        $("#PlacementClassId").combogrid({
            url: '{{ url('academic/class/placement/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', is_filter: 0 },
            onClickRow: function (index, row) {
                let capacities = row.capacity.split("/")
                if (parseInt(capacities[0]) == parseInt(capacities[1])) {
                    $.messager.alert('Peringatan', 'Kuota Kelas ' + row.class + ' sudah terpenuhi, silahkan pilih Kelas lainnya.', 'error')
                    $("#PlacementClassId").combogrid('clear')    
                    $("#PlacementSchoolYear").textbox("setValue", "")
                    $("#PlacementGrade").textbox("setValue", "")
                    $("#PlacementQuota").textbox("setValue", "")
                } else {
                    titlePlacement.innerText = row.class
                    $("#PlacementSchoolYear").textbox("setValue", row.schoolyear_id)
                    $("#PlacementGrade").textbox("setValue", row.grade_id)
                    $("#PlacementQuota").textbox("setValue", row.capacity)
                }
                $("#PlacementClassId").combogrid('hidePanel')
            }
        })
        $("#tb-placement-student").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 5,
            field: 'student_no'
        })
        $("#PlacementStudentClassId").combogrid({
            url: '{{ url('academic/student/placement/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#tb-student-placement").datagrid("reload", "{{ url('academic/student/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&fprospect_group=" + row.prospect_student_group_id + "&fclass=" + row.class_id).datagrid("enableFilter")
                $("#PlacementStudentDept").textbox("setValue", row.department)
                $("#PlacementStudentProcess").textbox("setValue", row.admission)
                $("#PlacementStudentSchoolYear").textbox("setValue", row.school_year)
                $("#PlacementStudentGrade").textbox("setValue", row.grade)
                $("#PlacementStudentGroup").textbox("setValue", row.groupname)
                $("#PlacementClassId").combogrid('hidePanel')
            }
        })
        $("#page-placement-main").waitMe({effect:"none"})
    })
    function newPlacement() {
        sessionStorage.formPSB_Penempatan = "active"
        $("#form-placement-main").form("reset")
        actionButtonPlacement("active", [0])
        markPlacement.innerText = "*"
        titlePlacement.innerText = ""
        idPlacement.value = "-1"
        $("#tb-placement-student").datagrid("loadData", [])
        $("#page-placement-main").waitMe("hide")
    }
    function savePlacement() {
        if (sessionStorage.formPSB_Penempatan == "active") {
            ajaxPlacement("academic/admission/placement/store")
        }
    }
    function ajaxPlacement(route) {
        var dg = $("#tb-placement-student").datagrid('getData')
        $("#form-placement-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-placement-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                if (response.success) {
                    Toast.fire({icon:"success",title:response.message})
                    actionClearPlacement()
                    $("#PlacementClassId").combogrid("grid").datagrid("reload")
                    $("#PlacementStudentClassId").combogrid("grid").datagrid("reload")
                    $("#tb-student-placement").datagrid("loadData", [])
                    $("#tb-placement-student").datagrid("loadData", [])
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                ("#page-placement-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                ("#page-placement-main").waitMe("hide")
            }
        })
        return false
    }
    function clearPlacement() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearPlacement()
            }
        })
    }
    function actionButtonPlacement(viewType, idxArray) {
        for (var i = 0; i < menuActionPlacement.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionPlacement[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionPlacement[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionPlacement[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionPlacement[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearPlacement() {
        sessionStorage.formPSB_Penempatan = "init"
        $("#form-placement-main").form("reset")
        actionButtonPlacement("init", [])
        titlePlacement.innerText = ""
        markPlacement.innerText = ""
        idPlacement.value = "-1"
        $("#tb-student-placement").datagrid("loadData", [])
        $("#tb-placement-student").datagrid("loadData", [])
        $("#page-placement-main").waitMe({effect:"none"})
    }
    function getProspectStudentDialog() {
        if ($("#PlacementProspectiveGroupId").combogrid("getValue") != "") {
            $("#place-prospect-student-w").window("open")
            $("#prospectDeptId").textbox("setValue", $("#PlacementDept").textbox("getValue"))
            $("#prospectAdmissionId").textbox("setValue", $("#PlacementProcess").textbox("getValue"))
            $("#prospectGroupId").textbox("setValue", $("#PlacementProspectiveGroupId").combogrid("getText"))
            $("#place-prospect-student-w").window("collapse")
            $("#place-prospect-student-w").window("expand")
            $("#tb-place-prospective-student").datagrid("reload", "{{ url('academic/admission/prospective-student/data/view') }}" + "?_token=" + "{{ csrf_token() }}" + "&fgroup=" + $("#PlacementProspectiveGroupId").combogrid("getValue"))
        }
    }
    function filterProspectStudent(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-place-prospective-student").datagrid("load", { params, _token: "{{ csrf_token() }}", fprospect_group: $('#PlacementProspectiveGroupId').combogrid('getValue') })
        } else {
            $("#tb-place-prospective-student").datagrid("load", { _token: "{{ csrf_token() }}", fprospect_group: $('#PlacementProspectiveGroupId').combogrid('getValue') })
        }
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
</script>