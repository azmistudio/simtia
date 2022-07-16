@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $WindowWidthleft = ($InnerWidth / 2 - 12) + 150 . "px";
    $SubGridHeight = $InnerHeight - 401 . "px";
    $ThirdGridHeight = $InnerHeight - 293 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Mutasi Santri</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportStudentMutation('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-student-mutation" class="panel-top">
            <a id="newStudentMutation" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newStudentMutation()">Baru</a>
            <a id="saveStudentMutation" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveStudentMutation()">Simpan</a>
            <a id="clearStudentMutation" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearStudentMutation()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-student-mutation"></span>Jenis Mutasi: <span id="title-student-mutation"></span></h6>
        </div>
        <div class="pt-3" id="page-student-mutation">
            <form id="form-student-mutation-main" method="post">
                <input type="hidden" id="id-student-mutation" name="id" value="-1" />
                <input type="hidden" id="id-student-mutation-dept" name="department_id" value="-1" />
                <input type="hidden" id="id-student-mutation-grade" name="grade_id" value="-1" />
                <input type="hidden" id="id-student-mutation-class" name="class_id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input name="department" id="StudentMutationDept" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year" id="StudentMutationSchoolyear" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="StudentMutationClassId" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas Awal:',
                                    labelWidth:'125px',
                                    panelWidth: 600,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:130},
                                        {field:'school_year',title:'Tahun Ajaran',width:100},
                                        {field:'grade',title:'Tingkat',width:100},
                                        {field:'class',title:'Kelas',width:170},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="grade" id="StudentMutationGrade" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <table id="tb-student-mutation-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,resizeable:true,sortable:true">NIS</th>
                                            <th data-options="field:'name',width:225,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:80,resizeable:true,sortable:true">Kelas</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <div class="mb-1">
                                <select name="mutation_id" id="StudentMutationId" class="easyui-combobox" style="width:335px;height:22px;" tabindex="1" data-options="label:'<b>*</b>Jenis Mutasi:',labelWidth:'125px',labelPosition:'before',panelHeight:100">
                                    <option value="">---</option>
                                    @foreach ($mutations as $mutation)
                                    <option value="{{ $mutation->id }}">{{ ucwords($mutation->name) }}</option>
                                    @endforeach
                                </select>
                                <span class="mr-2"></span>
                                <input name="mutation_date" id="StudentMutationDateDest" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal Mutasi:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <input name="remark" class="easyui-textbox" style="width:587px;height:22px;" data-options="label:'Keterangan:',labelWidth:'125px'" />
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div data-options="region:'center'">
        <div class="pt-3 pb-3">
            <form id="form-student-mutation" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="StudentMutationDeptDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="StudentMutationPeriodDest" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select id="StudentMutationType" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Jenis Mutasi:',
                                    labelWidth:'125px',
                                    panelWidth: 450,
                                    idField: 'seq',
                                    textField: 'mutation',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'period',title:'Tahun',width:80,align:'center'},
                                        {field:'mutation',title:'Jenis Mutasi',width:170},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div>
                                <table id="tb-student-mutated" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarStudentMutation">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:80,editor:'text',align:'center'">NIS</th>
                                            <th data-options="field:'student_name',width:180,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:100,align:'center'">Kelas Terakhir</th>
                                            <th data-options="field:'mutation_date',width:100,align:'center'">Tgl. Mutasi</th>
                                            <th data-options="field:'mutation_id',width:200">Jenis Mutasi</th>
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
    var menuActionStudentMutation = document.getElementById("menu-act-student-mutation").getElementsByTagName("a")
    var markStudentMutation = document.getElementById("mark-student-mutation")
    var titleStudentMutation = document.getElementById("title-student-mutation")
    var idStudentMutation = document.getElementById("id-student-mutation")
    var menubarStudentMutation = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-student-mutated').datagrid('getChecked')
            if (rows.length > 1) {
                $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
            } else {
                detailStudent(rows[0].student_id)
            }
        }
    },'-',{
        text: 'Hapus Terpilih',
        iconCls: 'ms-Icon ms-Icon--Delete',
        handler: function() {
            var rows = $('#tb-student-mutated').datagrid('getChecked')
            if (rows.length > 0) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Mutasi Santri terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/student/mutation/destroy') }}", {students: rows, _token: '{{ csrf_token() }}'}, function(response) {
                            if (response.success) {
                                $.messager.alert('Informasi', response.message)
                                actionClearStudentMutation()
                                $("#tb-student-mutated").datagrid("loadData", [])
                                $("#StudentMutationDeptDest").textbox("setValue", "")
                                $("#StudentMutationPeriodDest").textbox("setValue", "")
                                $("#StudentMutationType").combogrid("setValue", "")
                                $("#StudentMutationClassId").combogrid("grid").datagrid("reload")
                                $("#form-student-mutation").form("reset")
                            } else {
                                $.messager.alert('Peringatan', response.message, 'error')
                            }
                        })
                    }
                })
            }
        }
    }]
    $(function () {
        sessionStorage.formMutasi_Santri = "init"
        actionButtonStudentMutation("{{ $ViewType }}", [])
        $("#StudentMutationClassId").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 1, fcount: 1 },
            onClickRow: function (index, row) {
                $("#StudentMutationDept").textbox('setValue', row.department)
                $("#StudentMutationSchoolyear").textbox('setValue', row.school_year)
                $("#StudentMutationGrade").textbox('setValue', row.grade)
                $("#StudentMutationClassId").combogrid('hidePanel')
                $("#id-student-mutation-dept").val(row.department_id)
                $("#id-student-mutation-grade").val(row.grade_id)
                $("#id-student-mutation-class").val(row.id)
                $("#tb-student-mutation-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
            }
        })
        $("#StudentMutationId").combobox({
            onSelect: function (record) {
                if (record.value != "") {
                    titleStudentMutation.innerText = record.text
                }
            }
        })
        $("#StudentMutationType").combogrid({
            url: '{{ url('academic/student/mutation/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#StudentMutationDeptDest").textbox('setValue', row.department)
                $("#StudentMutationPeriodDest").textbox('setValue', row.period)
                $("#tb-student-mutated").datagrid("load", "{{ url('academic/student/mutation/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&ftype=" + row.mutation_id + "&fdept=" + row.department_id + "&fyear=" + row.period)
            }
        })
        $("#tb-student-mutation-student").datagrid("enableFilter")
        $("#tb-student-mutated").datagrid("enableFilter")
        $("#page-student-mutation").waitMe({effect:"none"})
    })
    function newStudentMutation() {
        sessionStorage.formMutasi_Santri = "active"
        $("#form-student-mutation-main").form("reset")
        actionButtonStudentMutation("active", [0])
        markStudentMutation.innerText = "*"
        titleStudentMutation.innerText = ""
        idStudentMutation.value = "-1"
        $("#tb-student-mutation-student").datagrid("loadData", [])
        $("#page-student-mutation").waitMe("hide")
    }
    function saveStudentMutation() {
        if (sessionStorage.formMutasi_Santri == "active") {
            var dg = $("#tb-student-mutation-student").datagrid('getChecked')
            if (dg.length > -1)
            {
                $("#form-student-mutation-main").ajaxSubmit({
                    url: "{{ url('academic/student/mutation/store') }}",
                    data: { _token: '{{ csrf_token() }}', students: dg },
                    beforeSubmit: function(formData, jqForm, options) {
                        $("#page-student-mutation").waitMe({effect:"facebook"})
                    },
                    success: function(response) {
                        if (response.success) {
                            Toast.fire({icon:"success",title:response.message})
                            actionClearStudentMutation()
                            $("#StudentMutationClassId").combogrid("grid").datagrid("reload")
                            $("#StudentMutationType").combogrid("grid").datagrid("reload")
                        } else {
                            $.messager.alert('Peringatan', response.message, 'error')
                        }
                        $("#page-student-mutation").waitMe("hide")
                    },
                    error: function(xhr) {
                        failResponse(xhr)
                        $("#page-student-mutation").waitMe("hide")
                    }
                })
            }
            return false
        }
    }
    function clearStudentMutation() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearStudentMutation()
            }
        })
    }
    function actionButtonStudentMutation(viewType, idxArray) {
        for (var i = 0; i < menuActionStudentMutation.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionStudentMutation[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionStudentMutation[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionStudentMutation[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionStudentMutation[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearStudentMutation() {
        sessionStorage.formMutasi_Santri = "init"
        $("#form-student-mutation-main").form("reset")
        actionButtonStudentMutation("init", [])
        titleStudentMutation.innerText = ""
        markStudentMutation.innerText = ""
        idStudentMutation.value = "-1"
        $("#tb-student-mutation-student").datagrid("loadData", [])
        $("#page-student-mutation").waitMe({effect:"none"})
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
    function exportStudentMutation(document) {
        var dg = $("#tb-student-mutated").datagrid('getData')
        if (dg.total > 0) {
            $.messager.progress({ title: "Ekspor dokumen ke PDF", msg: "Mohon tunggu..." })
            $.post("{{ url('academic/student/mutation/export-pdf') }}", $.param({ 
                _token: "{{ csrf_token() }}", 
                data: JSON.stringify(dg.rows),
                department: $("#StudentMutationDeptDest").combobox('getText'), 
                year: $("#StudentMutationPeriodDest").combobox('getText'), 
            }, true), function(response) {
                $.messager.progress("close")
                window.open("/storage/downloads/" + response)
            })            
        }
    }
    function filterStudentMutated(department_id, year, type) {
        $("#tb-student-mutated").datagrid("load", {_token: "{{ csrf_token() }}", fdept: department_id, fyear: year, ftype: type})
    }
</script>