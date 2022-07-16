@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 214 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Profil Lembaga Pendidikan</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div style="padding:5px;">
            <table id="tb-institute" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'deptid',width:120,resizeable:true,sortable:true">Departemen</th>
                        <th data-options="field:'name',width:180,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-institute" class="panel-top">
            <a id="newInstitute" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newInstitute()">Baru</a>
            <a id="editInstitute" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editInstitute()">Ubah</a>
            <a id="saveInstitute" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveInstitute()">Simpan</a>
            <a id="clearInstitute" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearInstitute()">Batal</a>
            <a id="deleteInstitute" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteInstitute()">Hapus</a>
            <a id="pdfInstitute" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfInstitute()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-institute"></span>Departemen Lembaga: <span id="title-institute"></span></h6>
        </div>
        <div id="page-institute" class="pt-3 pb-3">
            <form id="form-institute-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <input type="hidden" id="id-institute" name="id" value="-1" />
                            <div class="mb-1">
                                @if (auth()->user()->getDepartment->is_all != 1)
                                    <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                @else 
                                    <select name="department_id" id="InstituteDeptId" class="easyui-combobox" style="width:395px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                        @foreach ($depts as $dept)
                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                        @endforeach
                                    </select>
                                @endif
                            </div>
                            <div class="mb-1">
                                <input name="name" id="InstituteName" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="website" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'Website:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="email" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'Email:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="address" class="easyui-textbox" style="width:395px;height:100px;" data-options="label:'<b>*</b>Alamat:',labelWidth:'125px',multiline:true" />
                            </div>
                            <div class="mb-1">
                                <input name="phone" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'No. Telpon:',labelWidth:'125px'" />
                            </div>
                            <div class="mb-1">
                                <input name="fax" class="easyui-textbox" style="width:395px;height:22px;" data-options="label:'No. Faksimili:',labelWidth:'125px'" />
                            </div>
                        </div>
                        <div class="col-3">
                            <fieldset style="width:148px;margin-top:-7px;">
                                <legend>Logo:</legend>
                                <input name="logo" id="InstitutePhoto" class="easyui-filebox" data-options="prompt:'Gambar',buttonText:'Pilih',accept:'image/*'" style="width:100%">
                                <div class="mt-1 mb-1 img-preview">
                                    <img id="InstituteImgPreview" src="{{ asset('img/img-preview.png') }}" style="display:block;margin:auto;padding:auto;object-fit:cover;height:125px;width:125px;">
                                </div>
                                <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Delete'" onclick="clearPreview('InstitutePhoto','InstituteImgPreview')" style="width:125px;">Hapus</a>
                            </fieldset>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var menuActionInstitute = document.getElementById("menu-act-institute").getElementsByTagName("a")
    var titleInstitute = document.getElementById("title-institute")
    var markInstitute = document.getElementById("mark-institute")
    var idInstitute = document.getElementById("id-institute")
    var dgInstitute = $("#tb-institute")
    $(function () {
        sessionStorage.formLembaga = "init"
        dgInstitute.datagrid({
            url: "{{ url('institute/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formLembaga == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleInstitute.innerText = row.name
                    actionButtonInstitute("active",[2,3])
                    $("#form-institute-main").form("load", "{{ url('institute/show') }}" + "/" + row.id)
                    $("#InstitutePhoto").filebox("setText", row.logo)
                    if (row.logo === "" || row.logo === null) {
                        clearPreview("InstitutePhoto","InstituteImgPreview")
                    } else {
                        $("#InstituteImgPreview").attr("src", "/storage/uploads/" + row.logo)
                    }
                    $("#tt-institute").waitMe("hide")
                }
            }
        })
        dgInstitute.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgInstitute.datagrid('getPager').pagination())
        actionButtonInstitute("{{ $ViewType }}", [])
        $("#InstituteName").textbox("textbox").bind("keyup", function (e) {
            titleInstitute.innerText = $(this).val()
        })
        $("#InstitutePhoto").filebox({
            onChange: function(newValue, oldValue) {
                previewFile('InstitutePhoto','InstituteImgPreview')
            }
        })
        $("#tt-institute").waitMe({effect:"none"})
    })
    function newInstitute() {
        sessionStorage.formLembaga = "active"
        $("#form-institute-main").form("reset")
        actionButtonInstitute("active", [0,1,4,5])
        clearPreview("InstitutePhoto","InstituteImgPreview")
        markInstitute.innerText = "*"
        titleInstitute.innerText = ""
        idInstitute.value = "-1"
        $("#InstituteDeptId").combobox('textbox').focus()
        $("#tt-institute").waitMe("hide")
    }
    function editInstitute() {
        sessionStorage.formLembaga = "active"
        markInstitute.innerText = "*"
        actionButtonInstitute("active", [0,1,4,5])
    }
    function saveInstitute() {
        if (sessionStorage.formLembaga == "active") {
            ajaxInstitute("institute/store")
        }
    }
    function deleteInstitute() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Lembaga terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('institute/destroy') }}" +"/"+idInstitute.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxInstituteResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function pdfInstitute() {
        if (idInstitute.value != -1) {
            exportDocument("{{ url('institute/print') }}", { id: idInstitute.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxInstitute(route) {
        $("#form-institute-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-institute").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxInstituteResponse(response)
                $("#page-institute").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-institute").waitMe("hide")
            }
        })
        return false
    }
    function ajaxInstituteResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearInstitute()
            $("#tb-institute").datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearInstitute() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearInstitute()
            }
        })
    }
    function actionButtonInstitute(viewType, idxArray) {
        for (var i = 0; i < menuActionInstitute.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionInstitute[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionInstitute[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionInstitute[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionInstitute[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearInstitute() {
        sessionStorage.formLembaga = "init"
        $("#form-institute-main").form("reset")
        actionButtonInstitute("init", [])
        titleInstitute.innerText = ""
        markInstitute.innerText = ""
        idInstitute.value = "-1"
        clearPreview("InstitutePhoto","InstituteImgPreview")
        $("#tt-institute").waitMe({effect:"none"})
    }
</script>