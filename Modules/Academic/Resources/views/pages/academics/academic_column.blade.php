@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 275 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Tambahan Kolom Data Santri</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-column-prospective" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdepartment-column-prospective" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdepartment-column-prospective" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            @foreach ($depts as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterColumnProspective({fdepartment: $('#fdepartment-column-prospective').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-column-prospective').form('reset');filterColumnProspective({})">Batal</a>
                </div>
            </form>
            <table id="tb-column-prospective" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'name',width:150,resizeable:true,sortable:true">Kolom</th>
                        <th data-options="field:'type',width:100,resizeable:true,sortable:true">Tipe</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-column-prospective" class="panel-top">
            <a id="newColumnProspective" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newColumnProspective()">Baru</a>
            <a id="editColumnProspective" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editColumnProspective()">Ubah</a>
            <a id="saveColumnProspective" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveColumnProspective()">Simpan</a>
            <a id="clearColumnProspective" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearColumnProspective()">Batal</a>
            <a id="deleteColumnProspective" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteColumnProspective()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-column-prospective"></span>Kolom: <span id="title-column-prospective"></span></h6>
        </div>
        <div class="pt-3 pb-3" id="page-column-prospective-main">
            <form id="form-column-prospective-main" method="post">
                <input type="hidden" id="id-column-prospective" name="id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="ColumnProspectiveDeptId" class="easyui-combobox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                        @foreach ($depts as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="ColumnProspectiveName" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Nama Kolom:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <select name="type" class="easyui-combobox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Tipe:',labelWidth:'125px',labelPosition:'before',panelHeight:68">
                                    <option value="">---</option>
                                    <option value="1">TEKS</option>
                                    <option value="2">PILIHAN</option>
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="order" class="easyui-numberspinner" style="width:335px;height:22px;" data-options="label:'<b>*</b>Urutan:',labelWidth:'125px',min:0" />
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:335px;height:80px;" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="mb-1">
                                <a href="javascript:void(0)" id="columnOptionProspective" class="easyui-linkbutton" onclick="listColumnOption()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Database'">Atur Pilihan</a>
                            </div>
                            <ul id="listOptionProspective" class="easyui-datalist" title="Data Pilihan" lines="true" style="width:300px;height:158px"></ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- dialog --}}
<div id="column-prospective-w" class="easyui-window" title="Pilihan Data" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--List'" style="width:500px;height:300px;padding:10px;">
    <input type="hidden" id="id-column-prospect" value="" />
    <div class="mb-3">
        <input class="easyui-textbox" id="columnProspectId" style="width:335px;height:22px;" data-options="label:'Nama Kolom:',labelWidth:'125px',readonly:true" />
    </div>
    <div>
        <table id="tb-column-option-prospective" style="width:100%;height:200px" 
            data-options="idField:'id',rownumbers:'true',toolbar:'#toolbar-column-option',singleSelect:'true'">
            <thead>
                <tr>
                    <th data-options="field:'name',width:250,resizeable:true,sortable:true,editor:{type:'validatebox',options:{required:true}}">Item Pilihan</th>
                    <th data-options="field:'order',width:80,align:'center',resizeable:true,sortable:true,editor:{type:'numberbox',options:{required:true}}">Urutan</th>
                    <th data-options="field:'is_active',width:100,align:'center',resizeable:true,sortable:true,editor:{type:'checkbox',options:{on:'Ya',off:'Tidak'}}">Status</th>
                </tr>
            </thead>
        </table>
    </div>
    <div id="toolbar-column-option">
        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="javascript:$('#tb-column-option-prospective').edatagrid('addRow')">Baru</a>
        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="javascript:$('#tb-column-option-prospective').edatagrid('saveRow')">Simpan</a>
        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="javascript:$('#tb-column-option-prospective').edatagrid('cancelRow')">Batal</a>
        <a class="easyui-linkbutton" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="javascript:$('#tb-column-option-prospective').edatagrid('destroyRow')">Hapus</a>
    </div>
</div>
<script type="text/javascript">
    var menuActionColumnProspective = document.getElementById("menu-act-column-prospective").getElementsByTagName("a")
    var markColumnProspective = document.getElementById("mark-column-prospective")
    var titleColumnProspective = document.getElementById("title-column-prospective")
    var idColumnProspective = document.getElementById("id-column-prospective")
    var dgColumnProspective = $("#tb-column-prospective")
    $(function () {
        sessionStorage.formTambahan_Kolom = "init"
        dgColumnProspective.datagrid({
            url: "{{ url('academic/admission/column/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formTambahan_Kolom == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleColumnProspective.innerText = row.name
                    actionButtonColumnProspective("active",[2,3])
                    $("#form-column-prospective-main").form("load", "{{ url('academic/admission/column/show') }}" + "/" + row.id)
                    document.getElementById('id-column-prospect').value = row.id
                    if (row.type == "PILIHAN") {
                        $("#columnOptionProspective").linkbutton({disabled: false})
                    } else {
                        $("#columnOptionProspective").linkbutton({disabled: true})
                    }
                    $("#listOptionProspective").datalist({
                        url: "{{ url('academic/admission/column/option/data/list') }}" + "/" + row.id,
                        method: "get"
                    })
                    $("#page-column-prospective-main").waitMe("hide")
                }
            }
        })
        dgColumnProspective.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgColumnProspective.datagrid('getPager').pagination())
        actionButtonColumnProspective("{{ $ViewType }}", [])
        $("#ColumnProspectiveName").textbox("textbox").bind("keyup", function (e) {
            titleColumnProspective.innerText = $(this).val()
        })
        $('#column-prospective-w').window({
            onOpen:function(){
                $('#tb-column-option-prospective').edatagrid({
                    url: "{{ url('academic/admission/column/option/data') }}" + "/" + $('#id-column-prospect').val() + "?_token=" + "{{ csrf_token() }}",
                    saveUrl: "{{ url('academic/admission/column/option/store') }}" + "/" + $('#id-column-prospect').val() + "?_token=" + "{{ csrf_token() }}",
                    updateUrl: "{{ url('academic/admission/column/option/store') }}" + "/" + $('#id-column-prospect').val() + "?_token=" + "{{ csrf_token() }}",
                    destroyUrl: "{{ url('academic/admission/column/option/destroy') }}" + "?_token=" + "{{ csrf_token() }}"
                })
            }
        })
        $('#tb-column-option-prospective').edatagrid({
            onSave: function(index, row) {
                if (row.success) {
                    $.messager.alert('Informasi', row.message)
                    $("#listOptionProspective").datalist().datagrid('reload')
                } else {
                    $.messager.alert('Peringatan', row.message, 'error')
                }
                $(this).datagrid('reload')
            },
            onDestroy: function(index, row) {
                if (row.success) {
                    $.messager.alert('Informasi', row.message)
                    $("#listOptionProspective").datalist().datagrid('reload')
                } else {
                    $.messager.alert('Peringatan', row.message, 'error')
                }
                $(this).datagrid('reload')
            },
        })
        $("#page-column-prospective-main").waitMe({effect:"none"})
    })
    function filterColumnProspective(params) {
        if (Object.keys(params).length > 0) {
            dgColumnProspective.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgColumnProspective.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newColumnProspective() {
        sessionStorage.formTambahan_Kolom = "active"
        $("#form-column-prospective-main").form("reset")
        actionButtonColumnProspective("active", [0,1,4])
        markColumnProspective.innerText = "*"
        titleColumnProspective.innerText = ""
        idColumnProspective.value = "-1"
        $("#listOptionProspective").datalist().datagrid('loadData',[])
        $("#page-column-prospective-main").waitMe("hide")
    }
    function editColumnProspective() {
        sessionStorage.formTambahan_Kolom = "active"
        markColumnProspective.innerText = "*"
        actionButtonColumnProspective("active", [0, 1, 4])
    }
    function saveColumnProspective() {
        if (sessionStorage.formTambahan_Kolom == "active") {
            ajaxColumnProspective("academic/admission/column/store")
        }
    }
    function deleteColumnProspective() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Tambahan Kolom Calon Santri terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/admission/column/destroy') }}" +"/"+idColumnProspective.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxColumnProspectiveResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
                $("#listOptionProspective").datalist().datagrid('reload')
            }
        })
    }
    function ajaxColumnProspective(route) {
        $("#form-column-prospective-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-column-prospective-main").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxColumnProspectiveResponse(response)
                $("#page-column-prospective-main").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-column-prospective-main").waitMe("hide")
            }
        })
        return false
    }
    function ajaxColumnProspectiveResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearColumnProspective()
            $("#tb-column-prospective").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearColumnProspective() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearColumnProspective()
            }
        })
    }
    function actionButtonColumnProspective(viewType, idxArray) {
        for (var i = 0; i < menuActionColumnProspective.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionColumnProspective[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionColumnProspective[i].id).linkbutton({disabled: true})
                }
                $("#columnOptionProspective").linkbutton({disabled: true})
            } else {
                $("#" + menuActionColumnProspective[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionColumnProspective[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearColumnProspective() {
        sessionStorage.formTambahan_Kolom = "init"
        $("#form-column-prospective-main").form("reset")
        actionButtonColumnProspective("init", [])
        titleColumnProspective.innerText = ""
        markColumnProspective.innerText = ""
        idColumnProspective.value = "-1"
        $("#page-column-prospective-main").waitMe({effect:"none"})
    }
    function listColumnOption() {
        $('#column-prospective-w').window('open')
        $('#columnProspectId').textbox('setValue', $('#ColumnProspectiveName').textbox('getText'))
        $('#column-prospective-w').window('collapse')
        $('#column-prospective-w').window('expand')
    }
</script>