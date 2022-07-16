@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $WindowWidthleft = ($InnerWidth / 2 - 12) + 150 . "px";
    $SubGridHeight = $InnerHeight - 360 . "px";
    $ThirdGridHeight = $InnerHeight - 318 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Kelulusan - Alumni</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'" style="width:{{ $WindowWidthleft }}">
        <div id="menu-act-graduation-alumni" class="panel-top">
            <a id="newGraduationAlumni" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newGraduationAlumni()">Baru</a>
            <a id="saveGraduationAlumni" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveGraduationAlumni()">Simpan</a>
            <a id="clearGraduationAlumni" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearGraduationAlumni()">Batal</a>
        </div>
        <div class="title">
            <h6><span id="mark-graduation-alumni"></span>Kelas: <span id="title-graduation-alumni"></span></h6>
        </div>
        <div class="pt-3" id="page-graduation-alumni">
            <form id="form-graduation-alumni-main" method="post">
                <input type="hidden" id="id-graduation-alumni" name="id" value="-1" />
                <input type="hidden" id="id-graduation-alumni-dept" name="department_id" value="-1" />
                <input type="hidden" id="id-graduation-alumni-grade" name="grade_id" value="-1" />
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input name="department" id="GraduationAlumniDept" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                <span class="mr-2"></span>
                                <input name="school_year" id="GraduationAlumniSchoolYear" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="class_id" id="GraduationAlumniClass" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'125px',
                                    panelWidth: 600,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:130},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:130},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120,align:'center'},
                                    ]],
                                ">
                                </select>
                                <span class="mr-2"></span>
                                <input name="grade" id="GraduationAlumniGrade" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <input name="date" id="GraduationAlumniDate" class="easyui-datebox" style="width:240px;height:22px;" data-options="label:'<b>*</b>Tanggal Kelulusan:',labelWidth:'125px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <table id="tb-graduation-alumni-student" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                       data-options="method:'post',rownumbers:'true'">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100,resizeable:true,sortable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:200,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:80,resizeable:true,align:'center'">Kelas</th>
                                            <th data-options="field:'remark',width:180,editor:'text'">Keterangan</th>
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
        <div class="pt-3 pb-3">
            <form id="form-graduation-alumni-student" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-1">
                                <input id="GraduationAlumniYearLast" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tahun Kelulusan:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationAlumniDeptLast" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="GraduationAlumniGradeLast" class="easyui-textbox" style="width:335px;height:22px;" data-options="label:'Tingkat:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-3">
                                <select name="class_id" id="GraduationAlumniClassLast" class="easyui-combogrid" style="width:335px;height:22px;" tabindex="1" data-options="
                                    label:'Kelas:',
                                    labelWidth:'125px',
                                    panelWidth: 450,
                                    idField: 'seq',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'period',title:'Tahun Lulus',width:100},
                                        {field:'department',title:'Departemen',width:140},
                                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                                        {field:'class',title:'Kelas',width:150},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div>
                                <table id="tb-graduation-alumnid-student" class="easyui-datagrid" style="width:100%;height:{{ $ThirdGridHeight }}"
                                       data-options="method:'post',rownumbers:'true',toolbar:menubarGraduationAlumni">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'ck',checkbox:true"></th>
                                            <th data-options="field:'student_id',width:80,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:100">NIS</th>
                                            <th data-options="field:'name',width:240,resizeable:true,sortable:true">Nama Santri</th>
                                            <th data-options="field:'class',width:100,align:'center'">Kelas Terakhir</th>
                                            <th data-options="field:'graduate_date',width:100,align:'center'">Tanggal Lulus</th>
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
    var menuActionGraduationAlumni = document.getElementById("menu-act-graduation-alumni").getElementsByTagName("a")
    var markGraduationAlumni = document.getElementById("mark-graduation-alumni")
    var titleGraduationAlumni = document.getElementById("title-graduation-alumni")
    var idGraduationAlumni = document.getElementById("id-graduation-alumni")
    var menubarGraduationAlumni = [{
        text: 'Detail Santri',
        iconCls: 'ms-Icon ms-Icon--View',
        handler: function() {
            var rows = $('#tb-graduation-alumnid-student').datagrid('getChecked')
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
            var rows = $('#tb-graduation-alumnid-student').datagrid('getChecked')
            if (rows.length > 0) {
                $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kelulusan Santri terpilih, tetap lanjutkan?", function (r) {
                    if (r) {
                        $.post("{{ url('academic/graduation/alumni/destroy') }}", {students: rows, _token: '{{ csrf_token() }}'}, function(response) {
                            if (response.success) {
                                $.messager.alert("Informasi", response.message)
                                $("#GraduationAlumniYearLast").textbox("setValue", "")
                                $("#GraduationAlumniDeptLast").textbox("setValue", "")
                                $("#GraduationAlumniGradeLast").textbox("setValue", "")
                                $("#GraduationAlumniClassLast").combogrid("setValue", "")
                                $("#tb-graduation-alumnid-student").datagrid("loadData", [])
                                $("#GraduationAlumniClassLast").combogrid("grid").datagrid("reload")
                                $("#GraduationAlumniClass").combogrid("grid").datagrid("reload")
                                $("#form-graduation-alumni-student").form("reset")
                            } else {
                                $.messager.alert("Peringatan", response.message, 'error')
                            }
                        })
                    }
                })
            }
        }
    }]
    $(function () {
        sessionStorage.formKelulusan_Alumni = "init"
        actionButtonGraduationAlumni("{{ $ViewType }}", [1,2])
        $("#GraduationAlumniClass").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}', fstu_active: 1, fsem_active: 1, fscy_active: 2, fcount: 1 },
            onClickRow: function (index, row) {
                titleGraduationAlumni.innerText = row.class
                $("#id-graduation-alumni-dept").val(row.department_id)
                $("#id-graduation-alumni-grade").val(row.grade_id)
                $("#GraduationAlumniDept").textbox('setValue', row.department)
                $("#GraduationAlumniSchoolYear").textbox('setValue', row.school_year)
                $("#GraduationAlumniGrade").textbox('setValue', row.grade)
                $("#GraduationAlumni").combogrid('hidePanel')
                $("#tb-graduation-alumni-student").datagrid("load", "{{ url('academic/student/list') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.id)
                var end_dates = row.end_date.split("-")
                let end_date = new Date(end_dates[0], parseInt(end_dates[1]) - 1, end_dates[2])
                $("#GraduationAlumniDate").datebox().datebox('calendar').calendar({
                    validator: function(date){
                        var now = new Date()
                        return date >= end_date
                    }
                })
                $("#GraduationAlumniDate").datebox("setValue", row.end_date)
            }
        })
        $("#tb-graduation-alumni-student").datagrid("enableCellEditing").datagrid("enableFilter")
        $("#tb-graduation-alumnid-student").datagrid("enableFilter")
        $("#GraduationAlumniClassLast").combogrid({
            url: '{{ url('academic/graduation/alumni/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#GraduationAlumniYearLast").textbox('setValue', row.period)
                $("#GraduationAlumniDeptLast").textbox('setValue', row.department)
                $("#GraduationAlumniGradeLast").textbox('setValue', row.grade)
                $("#GraduationAlumniClassLast").combogrid('hidePanel')
                $("#tb-graduation-alumnid-student").datagrid("load", "{{ url('academic/graduation/alumni/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&fclass=" + row.end_class + "&fgrade=" + row.end_grade + "&fdept=" + row.department_id + "&fperiod=" + row.period)
            }
        })
        $("#page-graduation-alumni").waitMe({effect:"none"})
    })
    function newGraduationAlumni() {
        sessionStorage.formKelulusan_Alumni = "active"
        $("#form-graduation-alumni-main").form("reset")
        actionButtonGraduationAlumni("active", [0])
        markGraduationAlumni.innerText = "*"
        titleGraduationAlumni.innerText = ""
        idGraduationAlumni.value = "-1"
        $("#tb-graduation-alumni-student").datagrid("loadData", [])
        $("#page-graduation-alumni").waitMe("hide")
    }
    function saveGraduationAlumni() {
        if (sessionStorage.formKelulusan_Alumni == "active") {
            var dg = $("#tb-graduation-alumni-student").datagrid('getChecked')
            if (dg.length > -1)
            {
                $.messager.confirm("Konfirmasi", "Luluskan semua Santri terpilih?", function (r) {
                    if (r) {
                        $("#form-graduation-alumni-main").ajaxSubmit({
                            url: "{{ url('academic/graduation/alumni/store') }}",
                            data: { _token: '{{ csrf_token() }}', students: dg },
                            beforeSubmit: function(formData, jqForm, options) {
                                $("#page-graduation-alumni").waitMe({effect:"facebook"})
                            },
                            success: function(response) {
                                if (response.success) {
                                    Toast.fire({icon:"success",title:response.message})
                                    actionClearGraduationAlumni()
                                    $("#GraduationAlumniClassLast").combogrid("grid").datagrid("reload")
                                } else {
                                    $.messager.alert('Peringatan', response.message, 'error')
                                }
                                $("#page-graduation-alumni").waitMe("hide")
                            },
                            error: function(xhr) {
                                failResponse(xhr)
                                $("#page-graduation-alumni").waitMe("hide")
                            }
                        })
                    }
                })
            }
            return false
        }
    }
    function clearGraduationAlumni() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearGraduationAlumni()
            }
        })
    }
    function actionButtonGraduationAlumni(viewType, idxArray) {
        for (var i = 0; i < menuActionGraduationAlumni.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionGraduationAlumni[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionGraduationAlumni[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionGraduationAlumni[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionGraduationAlumni[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearGraduationAlumni() {
        sessionStorage.formKelulusan_Alumni = "init"
        $("#form-graduation-alumni-main").form("reset")
        actionButtonGraduationAlumni("init", [])
        titleGraduationAlumni.innerText = ""
        markGraduationAlumni.innerText = ""
        idGraduationAlumni.value = "-1"
        $("#tb-graduation-alumni-student").datagrid("loadData", [])
        $("#page-graduation-alumni").waitMe({effect:"none"})
    }
    function detailStudent(param) {
        exportDocument("{{ url('academic/student/print') }}", { id: param }, "Ekspor data ke PDF", "{{ csrf_token() }}")
    }
</script>