@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 301 . "px";
    $SubGridHeight = $InnerHeight - 352 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kartu Setoran Hafalan Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="printMemorizeCardForm('pdf')">Cetak Form Kartu Setoran</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-memorize-card" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    @if (auth()->user()->getDepartment->is_all != 1)
                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelWidth:100,readonly:true" />
                        <input type="hidden" id="fdept-memorize-card" value="{{ auth()->user()->department_id }}" />
                    @else 
                        <select id="fdept-memorize-card" class="easyui-combobox" style="width:285px;height:22px;" data-options="label:'Departemen:',labelPosition:'before',labelWidth:100,panelHeight:125">
                            <option value="">---</option>
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                <div class="mb-1">
                    <select id="fclass-memorize-card" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
                        label:'Kelas:',
                        labelWidth:100,
                        panelWidth: 470,
                        idField: 'id',
                        textField: 'class',
                        url: '{{ url('academic/class/student/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        queryParams: { _token: '{{ csrf_token() }}' },
                        columns: [[
                            {field:'department',title:'Departemen',width:110},
                            {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                            {field:'grade',title:'Tingkat',width:80,align:'center'},
                            {field:'class',title:'Kelas',width:120},
                        ]],
                    ">
                    </select>
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterMemorizeCard({fdept: $('#fdept-memorize-card').val(),fclass: $('#fclass-memorize-card').combobox('getValue')})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-memorize-card').form('reset');filterMemorizeCard({})">Batal</a>
                </div>
            </form>
            <table id="tb-memorize-card" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'class_id',width:170,resizeable:true,sortable:true">Kelas</th>
                        <th data-options="field:'memorize_date',width:80,align:'center'">Tanggal</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-memorize-card" class="panel-top">
            <a id="newMemorizeCard" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newMemorizeCard()">Baru</a>
            <a id="editMemorizeCard" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editMemorizeCard()">Ubah</a>
            <a id="saveMemorizeCard" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveMemorizeCard()">Simpan</a>
            <a id="clearMemorizeCard" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearMemorizeCard()">Batal</a>
            <a id="deleteMemorizeCard" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteMemorizeCard()">Hapus</a>
            <a id="pdfMemorizeCard" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfMemorizeCard()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-memorize-card"></span>Kelas: <span id="title-memorize-card"></span></h6>
        </div>
        <div class="pt-3 pb-3" id="page-memorize-card">
            <form id="form-memorize-card-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="id-memorize-card" name="id" value="-1" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="MemorizeCardDept" style="width:300px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input class="easyui-textbox" id="MemorizeCardSchoolYear" style="width:225px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'100px',readonly:true" />
                                <span class="mr-2"></span>
                                <input class="easyui-textbox" id="MemorizeCardGrade" style="width:138px;height:22px;" data-options="label:'Tingkat:',labelWidth:'75px',readonly:true" />
                                <span class="mr-2"></span>
                                <input class="easyui-textbox" id="MemorizeCardSemester" style="width:190px;height:22px;" data-options="label:'Semester:',labelWidth:'75px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="class_id" id="MemorizeCardClass" class="easyui-combogrid" style="width:300px;height:22px;" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'125px',
                                    panelWidth: 470,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:110},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:120},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <select name="employee_id" id="MemorizeCardEmployeeId" class="easyui-combogrid" style="width:375px;height:22px;" data-options="
                                    label:'<b>*</b>Guru:',
                                    labelWidth:'100px',
                                    panelWidth: 500,
                                    idField: 'id',
                                    textField: 'name',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'employee_id',title:'NIP',width:80},
                                        {field:'name',title:'Nama',width:200},
                                        {field:'section',title:'Bagian',width:250},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="memorize_date" id="MemorizeCardDate" class="easyui-datebox" style="width:190px;height:22px;" data-options="label:'<b>*</b>Tanggal:',labelWidth:'75px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <input name="remark" id="MemorizeCardRemark" class="easyui-textbox" style="width:889px;height:22px;" data-options="label:'Keterangan:',labelWidth:'125px'" />
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="mb-1">
                                <table id="tb-memorize-card-form" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}" 
                                    data-options="method:'post',rownumbers:'true', queryParams: { _token: '{{ csrf_token() }}' }">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'id',hidden:true">ID</th>
                                            <th data-options="field:'student_id',hidden:true">StudentID</th>
                                            <th data-options="field:'student_no',width:90,resizeable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:180,resizeable:true,align:'left'">Nama</th>
                                            <th data-options="field:'from_surah',width:220,resizeable:true,
                                                formatter:function(value,row){
                                                    return getSurahName(value)
                                                },
                                                editor: {
                                                    type: 'combobox',
                                                    options: {
                                                        valueField: 'id',
                                                        textField: 'name',
                                                        data: surahs
                                                    }
                                                }
                                            ">Dari Surat</th>
                                            <th data-options="field:'from_verse',width:70,resizeable:true,align:'center',editor:'numberbox'">Dari Ayat</th>
                                            <th data-options="field:'to_surah',width:220,resizeable:true,
                                                formatter:function(value,row){
                                                    return getSurahName(value)
                                                },
                                                editor: {
                                                    type: 'combobox',
                                                    options: {
                                                        valueField: 'id',
                                                        textField: 'name',
                                                        data: surahs
                                                    }
                                                }
                                            ">Sampai Surat</th>
                                            <th data-options="field:'to_verse',width:90,resizeable:true,align:'center',editor:'numberbox'">Sampai Ayat</th>
                                            <th data-options="field:'status',width:115,resizeable:true,
                                                editor: {
                                                    type: 'combobox',
                                                    options: {
                                                        valueField: 'id',
                                                        textField: 'id',
                                                        data: statusMemorize
                                                    }
                                                }
                                            ">Status</th>
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
    var menuActionMemorizeCard = document.getElementById("menu-act-memorize-card").getElementsByTagName("a")
    var titleMemorizeCard = document.getElementById("title-memorize-card")
    var markMemorizeCard = document.getElementById("mark-memorize-card")
    var idMemorizeCard = document.getElementById("id-memorize-card")
    var dgMemorizeCard = $("#tb-memorize-card")
    var surahs = [
        @foreach ($surahs as $surah)
        { id: {{ $surah->id }}, name: "{{ $surah->surah }}" },
        @endforeach
    ]
    var statusMemorize = [
        { id: 'SANGAT BAIK' },
        { id: 'BAIK' },
        { id: 'KURANG BAIK' }
    ]
    function getSurahName(value) {
        for (var i = 0; i < surahs.length; i++) {
            if (surahs[i].id == value) {
                return surahs[i].name
            } 
        }
    }
    $(function () {
        sessionStorage.formKartu_Setoran = "init"
        dgMemorizeCard.datagrid({
            url: "{{ url('academic/student/memorize-card/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formKartu_Setoran == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleMemorizeCard.innerText = row.class_id
                    actionButtonMemorizeCard("active",[2,3])
                    idMemorizeCard.value = 1
                    $("#form-memorize-card-main").form("load", "{{ url('academic/student/memorize-card/show') }}" + "/" + row.id_class + "/" + row.date)
                    $("#page-memorize-card").waitMe("hide")
                }
            }
        })
        dgMemorizeCard.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgMemorizeCard.datagrid('getPager').pagination())
        actionButtonMemorizeCard("{{ $ViewType }}", [])
        $("#MemorizeCardClass").combogrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                titleMemorizeCard.innerText = row.class
                $("#MemorizeCardDept").textbox('setValue', row.department)
                $("#MemorizeCardGrade").textbox('setValue', row.grade)
                $("#MemorizeCardSchoolYear").textbox('setValue', row.school_year)
                $("#MemorizeCardSemester").textbox('setValue', row.semester)
                $("#MemorizeCardClass").combogrid('hidePanel')
                $("#tb-memorize-card-form").datagrid("load", "{{ url('academic/student/memorize-card/data/card') }}" + "?class_id=" + row.id + "&memorize_date=1970-01-01")
            }
        })
        $("#MemorizeCardEmployeeId").combogrid({
            url: '{{ url('hr/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', section: 45 },
        })
        $("#tb-memorize-card-form").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 0,
            field: 'id'
        })
        $("#form-memorize-card-main").form({
            onLoadSuccess: function(data) {
                $("#MemorizeCardDept").textbox('setValue', data.department)
                $("#MemorizeCardGrade").textbox('setValue', data.grade)
                $("#MemorizeCardSchoolYear").textbox('setValue', data.school_year)
                $("#MemorizeCardSemester").textbox('setValue', data.semester)
                $("#MemorizeCardClass").combogrid("setValue", data.class_id)
                $("#MemorizeCardEmployeeId").combogrid("setValue", data.employee_id)
                $("#MemorizeCardDate").datebox("setValue", data.memorize_date)
                $("#MemorizeCardRemark").textbox("setValue", data.remark)
                $("#tb-memorize-card-form").datagrid("load", "{{ url('academic/student/memorize-card/data/card') }}" + "?class_id=" + data.class_id + "&memorize_date=" + data.memorize_date)
                $("#MemorizeCardClass").combogrid("readonly", true)
            }
        })
        $("#page-memorize-card").waitMe({effect:"hide"})
    })
    function filterMemorizeCard(params) {
        if (Object.keys(params).length > 0) {
            dgMemorizeCard.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgMemorizeCard.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newMemorizeCard() {
        sessionStorage.formKartu_Setoran = "active"
        $("#form-memorize-card-main").form("reset")
        actionButtonMemorizeCard("active", [0,1,4,5])
        markMemorizeCard.innerText = "*"
        titleMemorizeCard.innerText = ""
        idMemorizeCard.value = "-1"
        $("#tb-memorize-card-form").datagrid("loadData", [])
        $("#MemorizeCardClass").combogrid("readonly", false)
        $("#page-memorize-card").waitMe("hide")
    }
    function editMemorizeCard() {
        sessionStorage.formKartu_Setoran = "active"
        markMemorizeCard.innerText = "*"
        actionButtonMemorizeCard("active", [0,1,4])
    }
    function saveMemorizeCard() {
        if (sessionStorage.formKartu_Setoran == "active") {
            ajaxMemorizeCard("academic/student/memorize-card/store")
        }
    }
    function deleteMemorizeCard() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Setoran Hafalan terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                var dg = $("#tb-memorize-card-form").datagrid('getData')
                $.post("{{ url('academic/student/memorize-card/destroy') }}", { _token: "{{ csrf_token() }}", students: dg.rows }, "json").done(function( response ) {
                    ajaxMemorizeCardResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxMemorizeCard(route) {
        var dg = $("#tb-memorize-card-form").datagrid('getData')
        $("#form-memorize-card-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-memorize-card").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxMemorizeCardResponse(response)
                $("#page-memorize-card").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-memorize-card").waitMe("hide")
            }
        })
        return false
    }
    function ajaxMemorizeCardResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearMemorizeCard()
            $("#tb-memorize-card").datagrid("reload")
        } else {
            showError(response)
        }
    }
    function clearMemorizeCard() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearMemorizeCard()
            }
        })
    }
    function actionButtonMemorizeCard(viewType, idxArray) {
        for (var i = 0; i < menuActionMemorizeCard.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionMemorizeCard[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionMemorizeCard[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionMemorizeCard[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionMemorizeCard[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearMemorizeCard() {
        sessionStorage.formKartu_Setoran = "init"
        $("#form-memorize-card-main").form("reset")
        actionButtonMemorizeCard("init", [])
        titleMemorizeCard.innerText = ""
        markMemorizeCard.innerText = ""
        idMemorizeCard.value = "-1"
        $("#tb-memorize-card-form").datagrid("loadData", [])
        $("#MemorizeCardClass").combogrid("readonly", false)
        $("#page-memorize-card").waitMe({effect:"hide"})
    }
    function pdfMemorizeCard() {
        if (idMemorizeCard.value != -1) {
            var dg = $("#tb-memorize-card-form").datagrid('getData')
            exportDocument("{{ url('academic/student/memorize-card/print') }}", { 
                department: $("#MemorizeCardDept").textbox("getValue"),
                schoolyear: $("#MemorizeCardSchoolYear").textbox("getValue"),
                grade: $("#MemorizeCardGrade").textbox("getValue"),
                semester: $("#MemorizeCardSemester").textbox("getValue"),
                class: $("#MemorizeCardClass").combogrid("getText"),
                employee: $("#MemorizeCardEmployeeId").combogrid("getText"),
                memorize_date: $("#MemorizeCardDate").datebox("getValue"),
                remark: $("#MemorizeCardRemark").textbox("getValue"),
                students: dg.rows 
            }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function printMemorizeCardForm(id) {
        if (id != '') {
            exportDocument("{{ url('academic/student/memorize-card/print/form') }}", { id: id }, "Cetak Form Setoran Hafalan", "{{ csrf_token() }}")
        }
    }
</script>