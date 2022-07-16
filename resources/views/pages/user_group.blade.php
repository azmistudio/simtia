@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $GridHeight = $InnerHeight - 212 . "px";
    $ContentHeight = $InnerHeight - 312 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Grup Pengguna</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <table id="tb-group" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'name',width:200,resizeable:true,sortable:true">Nama Grup</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div id="GroupMainForm" data-options="region:'center'">
        <div id="menu-act-group" class="panel-top">
            <a id="newGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newGroup()">Baru</a>
            <a id="editGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editGroup()">Ubah</a>
            <a id="saveGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveGroup()">Simpan</a>
            <a id="clearGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearGroup()">Batal</a>
            <a id="deleteGroup" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteGroup()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-group"></span>Grup: <span id="title-group"></span></h6>
        </div>
        <div id="page-group" class="pt-3 pb-3">
            <form id="form-group-main" method="post">
                <input type="hidden" id="id-group" name="id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <input name="name" id="GroupName" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Nama Grup:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1" >
                                <table id="GroupPermission" style="width:100%;height:{{ $ContentHeight }}" class="easyui-treegrid" title="Daftar Hak Akses" data-options="url:'{{ url('group/permission') }}',method:'get',animate:true,idField:'id',treeField:'name'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'name'" width="500">Menu</th>
                                            <th data-options="field:'action'" width="500">Ijin Hak Akses</th>
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
    var menuActionGroup = document.getElementById("menu-act-group").getElementsByTagName("a")
    var markGroup = document.getElementById("mark-group")
    var titleGroup = document.getElementById("title-group")
    var idGroup = document.getElementById("id-group")
    var dgGroup = $("#tb-group")
    $(function () {
        sessionStorage.formGrup_Pengguna = "init"
        dgGroup.datagrid({
            url: "{{ url('group/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formGrup_Pengguna == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleGroup.innerText = row.name
                    actionButtonGroup("active",[2,3])
                    $("#form-group-main").form("load", "{{ url('group/show') }}" + "/" + row.id)
                    $("#page-group").waitMe("hide")
                }
            }
        })
        dgGroup.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgGroup.datagrid('getPager').pagination())
        actionButtonGroup("{{ $ViewType }}", [])
        $("#GroupName").textbox("textbox").bind("keyup", function (e) {
            titleGroup.innerText = $(this).val()
        })
        //
        $("#form-group-main").form({
            onLoadSuccess: function(data) {
                $('input[type=checkbox][name="permissions[]"]').prop('checked',false)
                if (data.permissions != "") {
                    var perms = data.permissions.split(",")
                    for (var i = 0; i < perms.length; i++) {
                        $("#permission"+perms[i]).each(function() { this.checked = true })
                    }
                }
            }
        })
        $("#page-group").waitMe({effect:"none"})
    })
    function checkAllMenu(checked) {
        $('input[type=checkbox][name="permissions[]"]').prop('checked',checked)
    }
    function checkSubMenu(param, checked) {
        var vals = param.id.split(",")
        for (var i = 0; i < vals.length; i++) {
            $("."+vals[i]).each(function() { this.checked = checked })
        }
    }
    function checkDetailMenu(param, checked) {
        $("."+param.id).each(function() { this.checked = checked })
    }
    function newGroup() {
        sessionStorage.formGrup_Pengguna = "active"
        $("#form-group-main").form("reset")
        actionButtonGroup("active", [0,1,4])
        markGroup.innerText = "*"
        titleGroup.innerText = ""
        idGroup.value = "-1"
        $("#GroupName").textbox('textbox').focus()
        $("#page-group").waitMe("hide")
    }
    function editGroup() {
        sessionStorage.formGrup_Pengguna = "active"
        markGroup.innerText = "*"
        actionButtonGroup("active", [0, 1, 4])
    }
    function saveGroup() {
        if (sessionStorage.formGrup_Pengguna == "active") {
            ajaxGroup("group/store")
        }
    }
    function deleteGroup() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Grup Pengguna terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('group/destroy') }}" +"/"+idGroup.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxGroupResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxGroup(route) {
        $("#form-group-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#GroupMainForm").waitMe({effect: 'facebook'})
            },
            success: function(response) {
                ajaxGroupResponse(response)
                $("#GroupMainForm").waitMe('hide')
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#GroupMainForm").waitMe('hide')
            }
        })
        return false
    }
    function ajaxGroupResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearGroup()
            dgGroup.datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearGroup() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearGroup()
            }
        })
    }
    function actionButtonGroup(viewType, idxArray) {
        for (var i = 0; i < menuActionGroup.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionGroup[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionGroup[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionGroup[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionGroup[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearGroup() {
        sessionStorage.formGrup_Pengguna = "init"
        $("#form-group-main").form("reset")
        actionButtonGroup("init", [])
        titleGroup.innerText = ""
        markGroup.innerText = ""
        idGroup.value = "-1"
        $("#page-group").waitMe({effect:"none"})
    }
</script>