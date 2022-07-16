@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 326 . "px";
    $TabHeight = $InnerHeight - 250 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Sumber Daya Manusia</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--ExcelDocument'" onclick="exportEmployee('excel')">Ekspor Excel</a>
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportEmployee('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width: {{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-employee" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select id="fsection-employee" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Bagian:',labelWidth:100,panelHeight:68">
                        <option value="">---</option>
                        @foreach ($sections as $section)
                        <option value="{{ $section->id }}">{{ $section->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-1">
                    <input id="fnip-employee" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'NIP:',labelWidth:100">
                </div>
                <div class="mb-1">
                    <input id="fname-employee" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Nama:',labelWidth:100">
                </div>
                <div style="margin-left: 100px;padding:5px 0">
                    <a class="easyui-linkbutton small-btn flist-box" onclick="filterEmployee({fsection: $('#fsection-employee').val(),fnip: $('#fnip-employee').val(),fname: $('#fname-employee').val()})">Cari</a>
                    <a class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-employee').form('clear');filterEmployee({})">Batal</a>
                    <a class="easyui-linkbutton small-btn ffull-box" onclick="$('#dd-employee').dialog('open')"><i class="ms-Icon ms-Icon--Search"></i></a>
                </div>
            </form>
            <table id="tb-employee" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'employee_id',width:60,resizeable:true,sortable:true,align:'center'">NIP</th>
                        <th data-options="field:'name',width:190,resizeable:true,sortable:true">Nama</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-employee" class="panel-top">
            <a id="newEmployee" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newEmployee()">Baru</a>
            <a id="editEmployee" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editEmployee()">Ubah</a>
            <a id="saveEmployee" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveEmployee()">Simpan</a>
            <a id="clearEmployee" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearEmployee()">Batal</a>
            <a id="deleteEmployee" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteEmployee()">Hapus</a>
            <a id="pdfEmployee" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfEmployee()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-employee"></span>Pegawai: <span id="title-employee"></span></h6>
        </div>
        <div id="page-employee">
            <form id="form-employee-main" method="post" enctype="multipart/form-data">
            <input type="hidden" id="id-employee" name="id" value="-1" />
                <div id="tt-employee" class="easyui-tabs borderless" plain="true" narrow="true" style="height:{{ $TabHeight }}">
                    <div title="Umum" class="content-doc pt-3">
                        <div class="container-fluid">
                            <div class="row row-cols-auto">
                                <div class="col">
                                    <div class="mb-1">
                                        <select name="section" id="EmployeeSectionId" class="easyui-combobox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Bagian:',labelWidth:'125px',labelPosition:'before',panelHeight:68">
                                            <option value="">---</option>
                                            @foreach ($sections as $section)
                                            <option value="{{ $section->id }}">{{ $section->name }}</option>
                                            @endforeach
                                        </select>
                                        <span class="mr-2"></span>
                                        <input name="national_id" class="easyui-textbox" style="width:335px;height:22px;" tabindex="12" data-options="label:'<b>*</b>No. Identitas:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="employee_id" id="EmployeeId" class="easyui-numberspinner" style="width:335px;height:22px;" tabindex="2" data-options="label:'NIP:',labelWidth:'125px',cls:'autogen',prompt:'Otomatis (jika kosong)'" />
                                        <span class="mr-2"></span>
                                        <input name="phone" class="easyui-textbox" style="width:335px;height:22px;" tabindex="13" data-options="label:'No. Telpon:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="name" id="EmployeeName" class="easyui-textbox" style="width:335px;height:22px;" tabindex="3" data-options="label:'<b>*</b>Nama:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="mobile" class="easyui-textbox" style="width:335px;height:22px;" tabindex="14" data-options="label:'<b>*</b>No. Handphone:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="title_first" class="easyui-textbox" style="width:335px;height:22px;" tabindex="4" data-options="label:'Gelar Awal:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="email" class="easyui-textbox" style="width:335px;height:22px;" tabindex="15" data-options="label:'<b>*</b>Email:',labelWidth:'125px'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="title_end" class="easyui-textbox" style="width:335px;height:22px;" tabindex="5" data-options="label:'Gelar Akhir:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="work_start" class="easyui-datebox" style="width:250px;height:22px;" tabindex="16" data-options="label:'<b>*</b>Tanggal Kerja:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="pob" class="easyui-textbox" style="width:335px;height:22px;" tabindex="6" data-options="label:'<b>*</b>Tempat Lahir:',labelWidth:'125px'" />
                                        <span class="mr-2"></span>
                                        <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" tabindex="17" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="dob" class="easyui-datebox" style="width:250px;height:22px;" tabindex="7" data-options="label:'<b>*</b>Tanggal Lahir:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                                    </div>
                                    <div class="mb-1">
                                        <label class="textbox-label textbox-label-before" style="text-align: left; width: 120px; height: 22px; line-height: 22px;"><b>*</b>Jenis Kelamin:</label>
                                        <input name="gender" class="easyui-radiobutton" value="1" data-options="label:'Laki-Laki',labelPosition:'after'" checked="checked" />
                                        <input name="gender" class="easyui-radiobutton" value="2" data-options="label:'Perempuan',labelPosition:'after'" />
                                    </div>
                                    <div class="mb-1">
                                        <select name="marital" class="easyui-combobox" style="width:335px;height:22px;" tabindex="9" data-options="label:'<b>*</b>Menikah:',labelWidth:'125px',labelPosition:'before',panelHeight:90">
                                            <option value="">---</option>
                                            <option value="1">Belum Menikah</option>
                                            <option value="2">Sudah Menikah</option>
                                            <option value="3">Janda/Duda</option>
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <select id="EmployeeTribeId" name="tribe" class="easyui-combobox" style="width:273px;height:22px;" tabindex="10" data-options="label:'<b>*</b>Suku:',labelWidth:'125px',labelPosition:'before',panelHeight:125,valueField:'id',textField:'name'">
                                            <option value="">---</option>
                                            @foreach ($tribes as $tribe)
                                            <option value="{{ $tribe->id }}">{{ $tribe->name }}</option>
                                            @endforeach
                                        </select>
                                        <a class="easyui-linkbutton small-btn" onclick="tribeDialog()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Add"></i></a>
                                        <a class="easyui-linkbutton small-btn" onclick="reloadSdmTribe('EmployeeTribeId')" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                                    </div>
                                    <div class="mb-1">
                                        <input name="address" class="easyui-textbox" style="width:335px;height:60px;" tabindex="11" data-options="label:'<b>*</b>Alamat:',labelWidth:'125px',multiline:true" />
                                        <span class="mr-2"></span>
                                        <input name="remark" class="easyui-textbox" style="width:335px;height:60px;" tabindex="18" data-options="label:'Keterangan:',labelWidth:'125px',multiline:true" />
                                    </div>
                                </div>
                                <div class="col-3">
                                    <fieldset style="width:148px;margin-top:-7px;">
                                        <legend>Foto:</legend>
                                        <input name="photo" id="EmployeePhoto" class="easyui-filebox" data-options="prompt:'Gambar',buttonText:'Pilih',accept:'image/*'" style="width:100%">
                                        <div class="mt-1 mb-1 img-preview">
                                            <img id="EmployeeImgPreview" src="{{ asset('img/img-preview.png') }}" style="display:block;margin:auto;padding:auto;object-fit:cover;height:125px;width:125px;">
                                        </div>
                                        <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--Delete'" onclick="clearPreview('EmployeePhoto','EmployeeImgPreview')" style="width:125px;">Hapus</a>
                                    </fieldset>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- dialogs -->
<div id="dd-employee" class="easyui-dialog p-2" title="Pencarian lanjut" style="width:330px;" data-options="iconCls:'ms-Icon ms-Icon--Search',resizable:true,modal:true,closed:true,buttons:'#bb-employee'">
    <form id="fff-employee" method="post">
    <div class="mb-1">
        <select id="ffsection-employee" class="easyui-combobox" style="width:300px;height:22px;" data-options="label:'Bagian:',panelHeight:68,labelWidth:'125px'">
            <option value="">---</option>
            @foreach ($sections as $section)
            <option value="{{ $section->id }}">{{ $section->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="mb-1">
        <input id="ffnip-employee" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'NIP:',labelWidth:'125px'">
    </div>
    <div class="mb-1">
        <input id="ffname-employee" class="easyui-textbox" style="width:300px;height:22px;" data-options="label:'Nama:',labelWidth:'125px'">
    </div>
    <div class="mb-1">
        <input id="ffdob-employee" class="easyui-datebox" style="width:235px;height:22px;" data-options="label:'Tanggal Lahir:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
    </div>
    <div class="mb-1">
        <select id="ffgender-employee" class="easyui-combobox" style="width:300px;height:22px;" data-options="label:'Jenis Kelamin:',panelHeight:68,labelWidth:'125px'">
            <option value="">---</option>
            <option value="1">Laki-Laki</option>
            <option value="2">Perempuan</option>
        </select>
    </div>
    <div class="mb-1">
        <select id="ffactive-employee" class="easyui-combobox" style="width:300px;height:22px;" data-options="label:'Status Aktif:',panelHeight:68,labelWidth:'125px'">
            <option value="">---</option>
            <option value="1">Aktif</option>
            <option value="2">Non Aktif</option>
        </select>
    </div>
    </form>
</div>
<div id="bb-employee">
	<a type="submit" class="easyui-linkbutton small-btn filter-box" onclick="
        filterEmployee({
            fsection: $('#ffsection-employee').val(), 
            fnip: $('#ffnip-employee').val(),
            fname: $('#ffname-employee').val(),
            fdob: $('#ffdob-employee').val(),
            fgender: $('#ffgender-employee').val(),
            factive: $('#ffactive-employee').val(),
        });$('#dd-employee').dialog('close')">Cari</a>
	<a class="easyui-linkbutton small-btn filter-box ml-0" onclick="$('#fff-employee').form('clear');filterEmployee({})">Batal</a>
</div>
<script type="text/javascript">
    var menuActionEmployee = document.getElementById("menu-act-employee").getElementsByTagName("a")
    var titleEmployee = document.getElementById("title-employee")
    var markEmployee = document.getElementById("mark-employee")
    var idEmployee = document.getElementById("id-employee")
    var dgEmployee = $("#tb-employee")
    $(function () {
        sessionStorage.formSdm = "init"
        dgEmployee.datagrid({
            url: "{{ url('hr/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formSdm == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleEmployee.innerText = row.name
                    $("#EmployeeId").numberspinner("readonly", true)
                    actionButtonEmployee("active",[2,3])
                    $("#form-employee-main").form("load", "{{ url('hr/show') }}" + "/" + row.id)
                    $("#EmployeePhoto").filebox("setText", row.photo)
                    if (row.photo === null || row.photo === "") {
                        clearPreview("EmployeePhoto","EmployeeImgPreview")
                    } else {
                        $("#EmployeeImgPreview").attr("src", "/storage/uploads/employee/" + row.photo)
                    }
                    $("#tt-employee").waitMe("hide")
                }
            }
        })
        dgEmployee.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgEmployee.datagrid('getPager').pagination())
        actionButtonEmployee("{{ $ViewType }}", [])
        $("#EmployeeName").textbox("textbox").bind("keyup", function (e) {
            titleEmployee.innerText = $(this).val()
        })
        $("#EmployeePhoto").filebox({
            onChange: function(newValue, oldValue) {
                previewFile('EmployeePhoto','EmployeeImgPreview')
            }
        })
        $("#tt-employee").waitMe({effect: 'none'})
    })
    function filterEmployee(params) {
        if (Object.keys(params).length > 0) {
            $("#tb-employee").datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            $("#tb-employee").datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newEmployee() {
        sessionStorage.formSdm = "active"
        $("#form-employee-main").form("reset")
        $("#EmployeeId").numberspinner("readonly", false)
        actionButtonEmployee("active", [0,1,4,5])
        clearPreview("EmployeePhoto","EmployeeImgPreview")
        markEmployee.innerText = "*"
        titleEmployee.innerText = ""
        idEmployee.value = "-1"
        $("#EmployeeSectionId").combobox('textbox').focus()
        $("#tt-employee").waitMe("hide")
    }
    function editEmployee() {
        sessionStorage.formSdm = "active"
        markEmployee.innerText = "*"
        actionButtonEmployee("active", [0, 1, 4, 5])
    }
    function saveEmployee() {
        if (sessionStorage.formSdm == "active") {
            ajaxEmployee("hr/store")
        }
    }
    function deleteEmployee() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Pegawai terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('hr/destroy') }}" +"/"+idEmployee.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxEmployeeResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function pdfEmployee() {
        if (idEmployee.value != -1) {
            exportDocument("{{ url('hr/print') }}", { id: idEmployee.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function ajaxEmployee(route) {
        $("#form-employee-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-employee").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxEmployeeResponse(response)
                $("#page-employee").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-employee").waitMe("hide")
            }
        })
        return false
    }
    function ajaxEmployeeResponse(response) {
        if (response.success) {
            if (response.params) {
                $.messager.alert({
                    title: "Informasi",
                    msg: "Anda telah mengubah alamat email, anda harus keluar sistem dan masuk kembali dengan alamat email baru.",
                    fn: function() {
                        exitApp("{{ url('logout') }}", "{{ csrf_token() }}")
                    }
                })
            } else {
                Toast.fire({icon:"success",title:response.message})
                actionClearEmployee()
                $("#tb-employee").datagrid("reload")
            }
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
    function clearEmployee() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearEmployee()
            }
        })
    }
    function actionButtonEmployee(viewType, idxArray) {
        for (var i = 0; i < menuActionEmployee.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionEmployee[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionEmployee[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionEmployee[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionEmployee[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearEmployee() {
        sessionStorage.formSdm = "init"
        $("#form-employee-main").form("reset")
        actionButtonEmployee("init", [])
        titleEmployee.innerText = ""
        markEmployee.innerText = ""
        idEmployee.value = "-1"
        clearPreview("EmployeePhoto","EmployeeImgPreview")
        $("#tt-employee").waitMe({effect: 'none'})
    }
    function exportEmployee(document) {
        var dg = $("#tb-employee").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('hr/export-') }}" + document,dg.rows,"Ekspor data Pegawai ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function reloadSdmTribe(id) {
        $('#'+id).combobox('reload','{{ url("reference/list") }}' + "/hr_tribe");
    }
</script>