@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth;
    $GridHeight = $InnerHeight - 212 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Departemen</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportDepartment('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <table id="tb-department" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'name',width:120,resizeable:true,sortable:true">Nama</th>
                        <th data-options="field:'employee_id',width:150,resizeable:true,sortable:true">Kepala Sekolah</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-department" class="panel-top">
            <a id="newDepartment" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newDepartment()">Baru</a>
            <a id="editDepartment" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editDepartment()">Ubah</a>
            <a id="saveDepartment" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveDepartment()">Simpan</a>
            <a id="clearDepartment" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearDepartment()">Batal</a>
            <a id="deleteDepartment" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteDepartment()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-department"></span>Departemen: <span id="title-department"></span></h6>
        </div>
        <div id="page-department" class="pt-3 pb-3">
            <form id="form-department-main" method="post">
                <input type="hidden" id="id-department" name="id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input name="name" id="DepartmentName" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <select name="employee_id" class="easyui-combogrid" style="width:335px;height:22px;" data-options="
                                    label:'<b>*</b>Kepala Sekolah:',
                                    labelWidth:'125px',
                                    panelWidth: 440,
                                    idField: 'id',
                                    textField: 'name',
                                    url: '{{ url('hr/combo-grid') }}',
                                    method: 'post',
                                    queryParams: { _token: '{{ csrf_token() }}' },
                                    mode:'remote',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'employee_id',title:'NIP',width:80},
                                        {field:'name',title:'Nama',width:200},
                                        {field:'section',title:'Bagian',width:150},
                                    ]],
                                ">
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
    var menuActionDepartment = document.getElementById("menu-act-department").getElementsByTagName("a")
    var markDepartment = document.getElementById("mark-department")
    var titleDepartment = document.getElementById("title-department")
    var idDepartment = document.getElementById("id-department")
    var dgDepartment = $("#tb-department")
    $(function () {
        sessionStorage.formDepartemen = "init"
        dgDepartment.datagrid({
            url: "{{ url('department/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formDepartemen == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleDepartment.innerText = row.name
                    actionButtonDepartment("active",[2,3])
                    $("#form-department-main").form("load", "{{ url('department/show') }}" + "/" + row.id)
                    $("#page-department").waitMe("hide")
                }
            }
        })
        dgDepartment.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgDepartment.datagrid('getPager').pagination())
        actionButtonDepartment("{{ $ViewType }}", [])
        $("#DepartmentName").textbox("textbox").bind("keyup", function (e) {
            titleDepartment.innerText = $(this).val()
        })
        $("#page-department").waitMe({effect:"none"})
    })
    function filterDepartment(params) {
        if (Object.keys(params).length > 0) {
            dgDepartment.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgDepartment.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newDepartment() {
        sessionStorage.formDepartemen = "active"
        $("#form-department-main").form("clear")
        actionButtonDepartment("active", [0,1,4])
        markDepartment.innerText = "*"
        titleDepartment.innerText = ""
        idDepartment.value = "-1"
        $("#DepartmentName").textbox('textbox').focus()
        $("#page-department").waitMe("hide")
    }
    function editDepartment() {
        sessionStorage.formDepartemen = "active"
        markDepartment.innerText = "*"
        actionButtonDepartment("active", [0, 1, 4])
    }
    function saveDepartment() {
        if (sessionStorage.formDepartemen == "active") {
            ajaxDepartment("department/store")
        }
    }
    function deleteDepartment() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Departemen terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('department/destroy') }}" +"/"+idDepartment.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxDepartmentResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxDepartment(route) {
        $("#form-department-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-department").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxDepartmentResponse(response)
                $("#page-department").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-department").waitMe("hide")
            }
        })
        return false
    }
    function ajaxDepartmentResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearDepartment()
            $("#tb-department").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearDepartment() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearDepartment()
            }
        })
    }
    function actionButtonDepartment(viewType, idxArray) {
        for (var i = 0; i < menuActionDepartment.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionDepartment[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionDepartment[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionDepartment[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionDepartment[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearDepartment() {
        sessionStorage.formDepartemen = "init"
        $("#form-department-main").form("clear")
        actionButtonDepartment("init", [])
        titleDepartment.innerText = ""
        markDepartment.innerText = ""
        idDepartment.value = "-1"
        $("#page-department").waitMe({effect:"none"})
    }
    function exportDepartment(document) {
        var dg = $("#tb-department").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('department/export-') }}" + document,dg.rows,"Ekspor data Departemen ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>