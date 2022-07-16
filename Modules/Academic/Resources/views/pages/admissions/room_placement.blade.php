@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $WindowWidthleft = ($InnerWidth / 2 - 12) + 150 . "px";
    $SubGridHeight = $InnerHeight - 358 . "px";
    $ThirdGridHeight = $InnerHeight - 289 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Pembagian Kamar Santri</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-room-placement" class="panel-top">
            <a id="newRoomPlacement" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newRoomPlacement()">Baru</a>
            <a id="saveRoomPlacement" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveRoomPlacement()">Simpan</a>
            <a id="clearRoomPlacement" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearRoomPlacement()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-room-placement"></span>Kamar Santri: <span id="title-room-placement"></span></h6>
        </div>
        <div class="pt-3" id="page-room-placement-main">
            <form id="form-room-placement-main" method="post">
                <input type="hidden" id="id-room-placement" name="id" value="-1" />
                <input type="hidden" id="id-room-placement-department" name="department_id" value="-1" />
                <input type="hidden" id="id-room-placement-gender" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="RoomPlacementDept" class="easyui-textbox" style="width:315px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="RoomPlacementEmployee" class="easyui-textbox" style="width:442px;height:22px;" data-options="label:'Penanggung Jawab:',labelWidth:'135px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="room_id" id="RoomPlacementRoomId" class="easyui-combogrid" style="width:315px;height:22px;" data-options="
                                    label:'<b>*</b>Kamar:',
                                    labelWidth:'125px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:120},
                                        {field:'name',title:'Nama',width:200},
                                        {field:'gender_name',title:'Status',width:110},
                                        {field:'quota',title:'Kapasitas/Terisi',width:100},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input id="RoomPlacementStatus" class="easyui-textbox" style="width:250px;height:22px;" data-options="label:'Status:',labelWidth:'135px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="RoomPlacementQuota" class="easyui-textbox" style="width:180px;height:22px;" data-options="label:'Kapasitas/Terisi:',labelWidth:'110px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <a href="javascript:void(0)" class="easyui-linkbutton" onclick="getStayingStudentDialog()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Database'">Ambil Data Santri</a>
                            </div>
                            <div class="mb-1">
                                <table id="tb-room-placement-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,resizeable:true,sortable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:325,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'gender_name',width:120,resizeable:true">Gender</th>
                                            <th data-options="field:'class',width:150,resizeable:true">Kelas</th>
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
    <div data-options="region:'center'">
        <div class="pt-3">
            <form id="form-student-room-placement" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="RoomPlacementStudentDept" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'100px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="RoomPlacementStudentStatus" class="easyui-textbox" style="width:190px;height:22px;" data-options="label:'Status:',labelWidth:'75px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="RoomPlacementStudentEmployee" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Png. Jawab:',labelWidth:'100px',readonly:true" />
                                <span class="mr-2"></span>
                                <input id="RoomPlacementStudentQuota" class="easyui-textbox" style="width:190px;height:22px;" data-options="label:'Kapasitas:',labelWidth:'75px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select id="RoomPlacementStudentClassId" class="easyui-combogrid" style="width:300px;height:22px;" data-options="
                                    label:'Kamar:',
                                    labelWidth:'100px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    pagination:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:120},
                                        {field:'name',title:'Nama',width:200},
                                        {field:'gender_name',title:'Status',width:110},
                                        {field:'quota',title:'Kapasitas/Terisi',width:100},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div>
                                <table id="tb-student-room-placement" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarRoomPlacement,pagination:'true',pageSize:50,pageList:[10,25,50,75,100],remoteFilter:true,clientPaging:false">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,editor:'text',align:'center'">NIS</th>
                                            <th data-options="field:'name',width:240,resizeable:true,sortable:true">Nama Santri</th>
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
<div id="room-placement-student-w" class="easyui-window" title="Daftar Santri" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:800px;height:410px;padding:10px;">
    <div>
        <div class="table-filter">
            <form id="fg-room-student" method="post">
            @csrf
            <table>
                <tbody>
                    <tr>
                        <td style="width:30px;"></td>
                        <td style="width:28px;"></td>
                        <td style="width:115px;">
                            <input class="easyui-textbox" id="fg-room-student-reg" style="width:110px;height:22px;" />
                        </td>
                        <td style="width:235px;">
                            <input class="easyui-textbox" id="fg-room-student-name" style="width:230px;height:22px;" />
                        </td>
                        <td style="width:100px;border-right: 0;text-align: left;">
                           <a href="javascript:void(0)" class="easyui-linkbutton" onclick="filterRoomStudent({fnis: $('#fg-room-student-reg').val(), fname: $('#fg-room-student-name').val()})" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                           <a href="javascript:void(0)" class="easyui-linkbutton" onclick="$('#fg-room-student').form('reset');filterRoomStudent({});" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                        </td>
                    </tr>
                </tbody>
            </table>
            </form>
        </div>
        <table id="tb-room-student" class="easyui-datagrid" style="width:100%;height:320px" 
            data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100],toolbar:toolbarRoomPlacement">
            <thead>
                <tr>
                    <th data-options="field:'ck',checkbox:true"></th>
                    <th data-options="field:'student_no',width:115,resizeable:true,sortable:true,align:'center'">NIS</th>
                    <th data-options="field:'name',width:235,resizeable:true,sortable:true">Nama</th>
                    <th data-options="field:'gender_name',width:120,resizeable:true">Gender</th>
                    <th data-options="field:'class',width:120,resizeable:true">Kelas</th>
                </tr>
            </thead>
        </table>
    </div>
</div>
<script type="text/javascript">
    var menuActionRoomPlacement = document.getElementById("menu-act-room-placement").getElementsByTagName("a")
    var markRoomPlacement = document.getElementById("mark-room-placement")
    var titleRoomPlacement = document.getElementById("title-room-placement")
    var idRoomPlacement = document.getElementById("id-room-placement")
    var toolbarRoomPlacement = [{
        text: 'Ambil Terpilih',
        iconCls: 'ms-Icon ms-Icon--SelectAll',
        handler: function() {
            var rows = $('#tb-room-student').datagrid('getSelections')
            $("#tb-room-placement-student").datagrid({
                data: rows
            })
            $('#room-placement-student-w').window('close')
        }
    }]
    var menubarRoomPlacement = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-student-room-placement').datagrid('getSelections')
            if (rows.length > 1) {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            } else {
                detailStudent(rows[0].student_id.toString())
            }
        }
    },'-',{
        text: 'Hapus Terpilih',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            var rows = $('#tb-student-room-placement').datagrid('getSelections')
            $.messager.confirm("Konfirmasi", "Anda akan menghapus data Penempatan Santri terpilih, tetap lanjutkan?", function (r) {
                if (r) {
                    $.post("{{ url('academic/room/placement/destroy') }}", {data: rows, _token: '{{ csrf_token() }}'}, function(response) {
                        if (response.success) {
                            $.messager.alert('Informasi', response.message)
                            actionClearRoomPlacement()
                            $("#RoomPlacementRoomId").combogrid("grid").datagrid("reload")
                            $("#RoomPlacementStudentClassId").combogrid("grid").datagrid("reload")
                            $("#tb-student-room-placement").datagrid("loadData", [])
                            $("#tb-room-placement-student").datagrid("loadData", [])
                            $("#form-student-room-placement").form("reset")
                        } else {
                            $.messager.alert('Peringatan', response.message, 'error')
                        }
                    })
                }
            })
        }
    }]
    $(function () {
        sessionStorage.formPembagian_Kamar = "init"
        actionButtonRoomPlacement("{{ $ViewType }}", [])
        $("#RoomPlacementRoomId").combogrid({
            url: "{{ url('general/room/combo-grid') }}",
            method: "post",
            mode: "remote",
            queryParams: { _token: "{{ csrf_token() }}", is_employee: 0 },
            onClickRow: function (index, row) {
                let capacities = row.quota.split("/")
                if (parseInt(capacities[0]) == parseInt(capacities[1])) {
                    $.messager.alert("Peringatan", "Kuota Kamar " + row.name + " sudah terpenuhi, silahkan pilih Kamar lainnya.", "error")
                    titleRoomPlacement.innerText = ""
                    $("#RoomPlacementRoomId").combogrid("clear")
                    $("#RoomPlacementDept").textbox("setValue", "")
                    $("#RoomPlacementEmployee").textbox("setValue", "")
                    $("#RoomPlacementStatus").textbox("setValue", "")
                    $("#RoomPlacementQuota").textbox("setValue", "")
                    $("#id-room-placement-department").val(-1)
                    $("#id-room-placement-gender").val(-1)
                } else {
                    titleRoomPlacement.innerText = row.name
                    $("#RoomPlacementDept").textbox("setValue", row.department)
                    $("#RoomPlacementEmployee").textbox("setValue", row.employee)
                    $("#RoomPlacementStatus").textbox("setValue", row.gender_name)
                    $("#RoomPlacementQuota").textbox("setValue", row.quota)
                    $("#id-room-placement-department").val(row.department_id)
                    $("#id-room-placement-gender").val(row.gender)
                    $("#RoomPlacementRoomId").combogrid("hidePanel")
                }
            }
        })
        $("#RoomPlacementStudentClassId").combogrid({
            url: "{{ url('general/room/combo-grid') }}",
            method: "post",
            mode:'remote',
            queryParams: { _token: "{{ csrf_token() }}", is_employee: 0 },
            onClickRow: function (index, row) {
                $("#tb-student-room-placement").datagrid("reload", "{{ url('academic/room/placement/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&room_id=" + row.id).datagrid("enableFilter")
                $("#RoomPlacementStudentDept").textbox("setValue", row.department)
                $("#RoomPlacementStudentEmployee").textbox("setValue", row.employee)
                $("#RoomPlacementStudentStatus").textbox("setValue", row.gender_name)
                $("#RoomPlacementStudentQuota").textbox("setValue", row.quota)
                $("#RoomPlacementStudentClassId").combogrid("hidePanel")
            }
        })
        $("#page-room-placement-main").waitMe({effect:"none"})
    })
    function newRoomPlacement() {
        sessionStorage.formPembagian_Kamar = "active"
        $("#form-room-placement-main").form("reset")
        actionButtonRoomPlacement("active", [0])
        markRoomPlacement.innerText = "*"
        titleRoomPlacement.innerText = ""
        idRoomPlacement.value = "-1"
        $("#tb-room-placement-student").datagrid("loadData", [])
        $("#page-room-placement-main").waitMe("hide")
    }
    function saveRoomPlacement() {
        if (sessionStorage.formPembagian_Kamar == "active") {
            ajaxRoomPlacement("academic/room/placement/store")
        }
    }
    function ajaxRoomPlacement(route) {
        var dg = $("#tb-room-placement-student").datagrid("getData")
        $("#form-room-placement-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-room-placement-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                if (response.success) {
                    Toast.fire({icon:"success",title:response.message})
                    actionClearRoomPlacement()
                    $("#RoomPlacementRoomId").combogrid("grid").datagrid("reload")
                    $("#RoomPlacementStudentClassId").combogrid("grid").datagrid("reload")
                    $("#tb-student-room-placement").datagrid("loadData", [])
                    $("#tb-room-placement-student").datagrid("loadData", [])
                } else {
                    $.messager.alert('Peringatan', response.message, 'error')
                }
                $("#page-room-placement-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-room-placement-main").waitMe("hide")
            }
        })
        return false
    }
    function clearRoomPlacement() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearRoomPlacement()
            }
        })
    }
    function actionButtonRoomPlacement(viewType, idxArray) {
        for (var i = 0; i < menuActionRoomPlacement.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionRoomPlacement[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionRoomPlacement[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionRoomPlacement[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionRoomPlacement[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearRoomPlacement() {
        sessionStorage.formPembagian_Kamar = "init"
        $("#form-room-placement-main").form("reset")
        actionButtonRoomPlacement("init", [])
        titleRoomPlacement.innerText = ""
        markRoomPlacement.innerText = ""
        idRoomPlacement.value = "-1"
        $("#tb-student-room-placement").datagrid("loadData", [])
        $("#tb-room-placement-student").datagrid("loadData", [])
        $("#page-room-placement-main").waitMe({effect:"none"})
    }
    function getStayingStudentDialog() {
        if ($("#RoomPlacementRoomId").combogrid("getValue") != "") {
            $("#room-placement-student-w").window("open")
            $("#room-placement-student-w").window("collapse")
            $("#room-placement-student-w").window("expand")
            $("#tb-room-student").datagrid("reload", "{{ url('academic/student/data/room') }}" + "?_token=" + "{{ csrf_token() }}" + "&params[fdepartment]=" + $("#id-room-placement-department").val() + "&params[fgender]=" + $("#id-room-placement-gender").val())
        }
    }
    function filterRoomStudent(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-room-student").datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            $("#tb-room-student").datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
</script>