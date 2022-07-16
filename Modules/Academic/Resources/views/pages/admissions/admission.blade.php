@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Proses Penerimaan Santri Baru</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAdmission('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-admission" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',readonly:true,labelWidth:100" />
                        <input type="hidden" id="fdept-admission" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-admission" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125,valueField:'id',textField:'name'">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <input id="fname-admission" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterAdmission({fdept: $('#fdept-admission').val(),fname: $('#fname-admission').val()})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-admission').form('reset');filterAdmission({})">Batal</a>
                </div>
            </form>
            <table id="tb-admission" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" 
                data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100],
                    rowStyler:function (index, row) { if (row.is_active === 'Aktif') { return 'font-weight:600' } }">
                <thead>
                    <tr>
                        <th data-options="field:'name',width:180,resizeable:true,sortable:true">Nama</th>
                        <th data-options="field:'total_admission',width:80,resizeable:true,sortable:true">Jumlah</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-admission" class="panel-top">
            <a id="newAdmission" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newAdmission()">Baru</a>
            <a id="editAdmission" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editAdmission()">Ubah</a>
            <a id="saveAdmission" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveAdmission()">Simpan</a>
            <a id="clearAdmission" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearAdmission()">Batal</a>
            <a id="deleteAdmission" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteAdmission()">Hapus</a>
        </div>
        <div class="title">
            <h6><span id="mark-admission"></span>Proses Penerimaan: <span id="title-admission"></span></h6>
        </div>
        <div id="page-admission" class="pt-3 pb-3">
            <form id="form-admission-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-admission" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="AdmissionDeptId" class="easyui-combobox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                        @foreach ($departments as $department)
                                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="AdmissionName" class="easyui-textbox" style="width:335px;height:44px;" data-options="label:'<b>*</b>Nama:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="prefix" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'<b>*</b>Awalan (Prefix):',labelWidth:'125px'" />
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
    var menuActionAdmission = document.getElementById("menu-act-admission").getElementsByTagName("a")
    var titleAdmission = document.getElementById("title-admission")
    var markAdmission = document.getElementById("mark-admission")
    var idAdmission = document.getElementById("id-admission")
    var dgAdmission = $("#tb-admission")
    $(function () {
        sessionStorage.formPSB_Proses = "init"
        dgAdmission.datagrid({
            url: "{{ url('academic/admission/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPSB_Proses == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleAdmission.innerText = row.name
                    actionButtonAdmission("active",[2,3])
                    $("#form-admission-main").form("load", "{{ url('academic/admission/show') }}" + "/" + row.id)
                    $("#page-admission").waitMe("hide")
                }
            }
        })
        dgAdmission.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgAdmission.datagrid('getPager').pagination())
        actionButtonAdmission("{{ $ViewType }}", [])
        $("#AdmissionName").textbox("textbox").bind("keyup", function (e) {
            titleAdmission.innerText = $(this).val()
        })
        $("#page-admission").waitMe({effect:"none"})
    })
    function filterAdmission(params) {
        if (Object.keys(params).length > 0) {
            dgAdmission.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgAdmission.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newAdmission() {
        sessionStorage.formPSB_Proses = "active"
        $("#form-admission-main").form("reset")
        actionButtonAdmission("active", [0,1,4])
        markAdmission.innerText = "*"
        titleAdmission.innerText = ""
        idAdmission.value = "-1"
        $("#AdmissionDeptId").combobox('textbox').focus()
        $("#page-admission").waitMe("hide")
    }
    function editAdmission() {
        sessionStorage.formPSB_Proses = "active"
        markAdmission.innerText = "*"
        actionButtonAdmission("active", [0,1,4])
    }
    function saveAdmission() {
        if (sessionStorage.formPSB_Proses == "active") {
            ajaxAdmission("academic/admission/store")
        }
    }
    function deleteAdmission() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Proses Penerimaan terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/admission/destroy') }}" +"/"+idAdmission.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxAdmissionResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })  
            }
        })
    }
    function pdfAdmission() {
        if (idAdmission.value != -1) {
            exportDocument("{{ url('academic/admission/print') }}", { id: idAdmission.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxAdmission(route) {
        $("#form-admission-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-admission").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxAdmissionResponse(response)
                $("#page-admission").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-admission").waitMe("hide")
            }
        })
        return false
    }
    function ajaxAdmissionResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearAdmission()
            $("#tb-admission").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearAdmission() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearAdmission()
            }
        })
    }
    function actionButtonAdmission(viewType, idxArray) {
        for (var i = 0; i < menuActionAdmission.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionAdmission[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionAdmission[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionAdmission[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionAdmission[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearAdmission() {
        sessionStorage.formPSB_Proses = "init"
        $("#form-admission-main").form("reset")
        actionButtonAdmission("init", [])
        titleAdmission.innerText = ""
        markAdmission.innerText = ""
        idAdmission.value = "-1"
        $("#page-admission").waitMe({effect:"none"})
    }
    function exportAdmission(document) {
        var dg = $("#tb-admission").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/admission/export-') }}" + document,dg.rows,"Ekspor data PSB Proses ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>