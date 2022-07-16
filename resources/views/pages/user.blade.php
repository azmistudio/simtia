@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $GridHeight = $InnerHeight - 274 . "px";
    $TabHeight = $InnerHeight - 224 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Pengguna</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-user" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <input id="fname" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterUser({fname: $('#fname').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-user').form('clear');filterUser({})">Batal</a>
                </div>
            </form>
            <table id="tb-user" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'name',width:120,resizeable:true,sortable:true">Nama</th>
                        <th data-options="field:'email',width:150,resizeable:true,sortable:true">Email</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-user" class="panel-top">
            <a id="newUser" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newUser()">Baru</a>
            <a id="editUser" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editUser()">Ubah</a>
            <a id="saveUser" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveUser()">Simpan</a>
            <a id="clearUser" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearUser()">Batal</a>
            <a id="deleteUser" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteUser()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-user"></span>Pengguna: <span id="title-user"></span></h6>
        </div>
        <div id="page-user" class="pt-3 pb-3">
            <form id="form-user-main" method="post">
                <input type="hidden" id="id-user" name="id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <select name="department_id" id="UserDeptId" class="easyui-combobox" style="width:454px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Departemen:',labelWidth:'175px',labelPosition:'before',panelHeight:92">
                                    @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-1">
                                <select name="name" id="UserId" class="easyui-combogrid" style="width:454px;height:22px;" data-options="
                                    label:'<b>*</b>Nama:',
                                    labelWidth:'175px',
                                    panelWidth: 570,
                                    idField: 'name',
                                    textField: 'name',
                                    url: '{{ url('hr/combo-grid') }}',
                                    method: 'post',
                                    mode:'remote',
                                    queryParams: { _token: '{{ csrf_token() }}' },
                                    fitColumns:true,
                                    columns: [[
                                        {field:'employee_id',title:'NIP',width:80,align:'center'},
                                        {field:'name',title:'Nama',width:270},
                                        {field:'email',title:'Email',width:300},
                                        {field:'section',title:'Bagian',width:250},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="email" id="UserEmail" class="easyui-textbox" style="width:454px;height:22px;" data-options="label:'Email (akun akses):',labelWidth:'175px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input name="password" id="UserPass" type="password" class="easyui-textbox" style="width:454px;height:22px;" data-options="label:'<b>*</b>Kata Sandi:',labelWidth:'175px'" />
                            </div>
                            <div class="mb-1">
                                <input name="password_conf" id="UserPassConf" type="password" class="easyui-textbox" style="width:454px;height:22px;" data-options="label:'<b>*</b>Konfirmasi Kata Sandi:',labelWidth:'175px'" />
                            </div>
                            <div class="mb-1">
                                <select name="roles" class="easyui-combobox" style="width:454px;height:22px;" data-options="label:'<b>*</b>Grup Pengguna:',labelWidth:'175px',labelPosition:'before',panelHeight:125">
                                    <option value="">---</option>
                                    @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionUser = document.getElementById("menu-act-user").getElementsByTagName("a")
    var markUser = document.getElementById("mark-user")
    var titleUser = document.getElementById("title-user")
    var idUser = document.getElementById("id-user")
    var dgUser = $("#tb-user")
    $(function () {
        sessionStorage.formPengguna = "init"
        dgUser.datagrid({
            url: "{{ url('user/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPengguna == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleUser.innerText = row.name
                    actionButtonUser("active",[2,3])
                    $("#form-user-main").form("load", "{{ url('user/show') }}" + "/" + row.id)
                    $("#UserPass").textbox("setValue", "default-no-change")
                    $("#UserPassConf").textbox("setValue", "default-no-change")
                    $("#page-user").waitMe("hide")
                }
            }
        })
        dgUser.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgUser.datagrid('getPager').pagination())
        actionButtonUser("{{ $ViewType }}", [])
        $("#UserId").combogrid({
            onClickRow: function (index, row) {
                titleUser.innerText = row.name
                $("#UserEmail").textbox("setValue", row.email)
                $("#UserEmail").textbox("setText", row.email)
            }
        })
        $("#page-user").waitMe({effect:"none"})
    })
    function filterUser(params) {
        if (Object.keys(params).length > 0) {
            dgUser.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgUser.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newUser() {
        sessionStorage.formPengguna = "active"
        $("#form-user-main").form("reset")
        actionButtonUser("active", [0,1,4])
        markUser.innerText = "*"
        titleUser.innerText = ""
        idUser.value = "-1"
        $("#UserId").combobox('textbox').focus()
        $("#page-user").waitMe("hide")
    }
    function editUser() {
        sessionStorage.formPengguna = "active"
        markUser.innerText = "*"
        actionButtonUser("active", [0, 1, 4])
    }
    function saveUser() {
        if (sessionStorage.formPengguna == "active") {
            ajaxUser("user/store")
        }
    }
    function deleteUser() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Pengguna terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('user/destroy') }}" +"/"+idUser.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxUserResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxUser(route) {
        $("#form-user-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-user").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxUserResponse(response)
                $("#page-user").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-user").waitMe("hide")
            }
        })
        return false
    }
    function ajaxUserResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearUser()
            dgUser.datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearUser() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearUser()
            }
        })
    }
    function actionButtonUser(viewType, idxArray) {
        for (var i = 0; i < menuActionUser.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionUser[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionUser[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionUser[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionUser[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearUser() {
        sessionStorage.formPengguna = "init"
        $("#form-user-main").form("reset")
        actionButtonUser("init", [])
        titleUser.innerText = ""
        markUser.innerText = ""
        idUser.value = "-1"
        $("#page-user").waitMe({effect:"none"})
    }
</script>