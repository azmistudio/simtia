@php
    $WindowHeight = $InnerHeight - 276 . "px";
    $WindowWidth = $InnerWidth - 231 . "px";
    $TabHeight = $InnerHeight - 169 . "px";
    $TabContentHeight = $InnerHeight - 227 . "px";
    $TabGridHeight = $InnerHeight - 271 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Referensi Akademik</h5>
        </div>
        <div class="col-4 p-0 text-right"></div>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 p-0">
            <div id="tt-reference-acd" class="easyui-tabs" style="height:{{ $TabHeight }}" data-options="plain:true,narrow:true,tabPosition:'left',headerWidth:200">
                <div title="Tingkat" class="p-1">
                    <div class="container-fluid mb-2">
                        <div class="row">
                            <div class="col-12 p-0 text-right">
                                <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAcademicRef('tb-ref-grade','{{ url('academic/grade/export-') }}','pdf','Tingkat')">Ekspor PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="easyui-layout" style="height:{{ $TabContentHeight }};width:{{ $WindowWidth }};">
                        <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
                            <div class="p-1">
                                <table id="tb-ref-grade" class="easyui-datagrid" style="width:100%;height:{{ $TabGridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'department_id',width:100,resizeable:true,sortable:true">Departemen</th>
                                            <th data-options="field:'grade',width:80,resizeable:true,sortable:true,align:'center'">Tingkat</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div data-options="region:'center'">
                            <div id="menu-act-grade" class="panel-top">
                                <a id="newGrade" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newGrade()">Baru</a>
                                <a id="editGrade" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editGrade()">Ubah</a>
                                <a id="saveGrade" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveGrade()">Simpan</a>
                                <a id="clearGrade" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearGrade()">Batal</a>
                                <a id="deleteGrade" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteGrade()">Hapus</a>
                            </div>
                            <div class="title">
                                <h6><span id="mark-grade"></span>Tingkat: <span id="title-grade"></span></h6>
                            </div>
                            <div class="p-3" id="page-grade">
                                <form id="form-grade-main" method="post">
                                <input type="hidden" id="id-grade" name="id" value="-1" />
                                <div class="mb-1">
                                    @if (auth()->user()->getDepartment->is_all != 1)
                                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                    @else
                                        <select name="department_id" id="GradeDeptId" class="easyui-combobox" style="width:375px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                            @foreach ($depts as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="mb-1">
                                    <input name="grade" id="GradeId" class="easyui-numberspinner" style="width:200px;height:22px;" data-options="label:'<b>*</b>Tingkat:',labelWidth:'125px',labelPosition:'before',min:1" />
                                    <span class="mr-2 @if (auth()->user()->getDepartment->is_all != 1) d-none @endif">
                                        <input name="is_all" id="GradeIdAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Departemen',labelWidth:'140px',labelPosition:'after'" />
                                    </span>
                                </div>
                                <div class="mb-1">
                                    <input name="remark" class="easyui-textbox" style="width:375px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',labelPosition:'before',multiline:true" />
                                </div>
                                <div class="mb-1">
                                    <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div title="Tahun Ajaran" class="p-1">
                    <div class="container-fluid mb-2">
                        <div class="row">
                            <div class="col-12 p-0 text-right">
                                <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAcademicRef('tb-ref-schoolyear','{{ url('academic/school-year/export-') }}','pdf','Tahun Ajaran')">Ekspor PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="easyui-layout" style="height:{{ $TabContentHeight }};width:{{ $WindowWidth }};">
                            <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
                                <div class="p-1">
                                    <table id="tb-ref-schoolyear" class="easyui-datagrid" style="width:100%;height:{{ $TabGridHeight }}"
                                        data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100],
                                            rowStyler:function (index, row) { if (row.is_active === 'Aktif') { return 'font-weight:600' } }">
                                        <thead>
                                            <tr>
                                                <th data-options="field:'department_id',width:100,resizeable:true,sortable:true">Departemen</th>
                                                <th data-options="field:'school_year',width:100,resizeable:true,sortable:true">Tahun Ajaran</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                            <div data-options="region:'center'">
                                <div id="menu-act-schoolyear" class="panel-top">
                                    <a id="newSchoolYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newSchoolYear()">Baru</a>
                                    <a id="editSchoolYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editSchoolYear()">Ubah</a>
                                    <a id="saveSchoolYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveSchoolYear()">Simpan</a>
                                    <a id="clearSchoolYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearSchoolYear()">Batal</a>
                                    <a id="deleteSchoolYear" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteSchoolYear()">Hapus</a>
                                </div>
                                <div class="title">
                                    <h6><span id="mark-schoolyear"></span>Tahun Ajaran: <span id="title-schoolyear"></span></h6>
                                </div>
                                <div class="p-3" id="page-schoolyear">
                                    <form id="form-schoolyear-main" method="post">
                                    <input type="hidden" id="id-schoolyear" name="id" value="-1" />
                                    <div class="mb-1">
                                        <select name="department_id" id="SchoolYearDeptId" class="easyui-combobox" style="width:375px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                            <option value="1">TAHFIDZ (Semua Departemen)</option>
                                        </select>
                                    </div>
                                    <div class="mb-1">
                                        <input name="school_year" id="SchoolYearId" class="easyui-numberspinner" style="width:200px;height:22px;" data-options="label:'<b>*</b>Tahun Ajaran:',labelWidth:'125px',labelPosition:'before',min:2020" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="date_start" class="easyui-datebox" style="width:235px;height:22px;" data-options="label:'<b>*</b>Tanggal Mulai:',labelWidth:'125px',labelPosition:'before',formatter:dateFormatter,parser:dateParser" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="date_end" class="easyui-datebox" style="width:235px;height:22px;" data-options="label:'<b>*</b>Tanggal Akhir:',labelWidth:'125px',labelPosition:'before',formatter:dateFormatter,parser:dateParser" />
                                    </div>
                                    <div class="mb-1">
                                        <input name="remark" class="easyui-textbox" style="width:375px;height:40px;" data-options="label:'Keterangan:',labelWidth:'125px',labelPosition:'before',multiline:true" />
                                    </div>
                                    <div class="mb-5">
                                        <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                                    </div>
                                    </form>
                                    <p class="well">
                                        <label style="font-weight:500">Penting:</label><br/>
                                        Menu ini mengatur tahun ajaran yang ada di departemen sekolah. Setiap pergantian tahun ajaran, pengguna harus mengubah status aktif tahun ajaran ini.
                                    </p>
                                </div>
                            </div>
                    </div>
                </div>
                <div title="Semester" class="p-1">
                    <div class="container-fluid mb-2">
                        <div class="row">
                            <div class="col-12 p-0 text-right">
                                <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAcademicRef('tb-ref-semester','{{ url('academic/semester/export-') }}','pdf','Semester')">Ekspor PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="easyui-layout" style="height:{{ $TabContentHeight }};width:{{ $WindowWidth }};">
                        <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
                            <div class="p-1">
                                <table id="tb-ref-semester" class="easyui-datagrid" style="width:100%;height:{{ $TabGridHeight }}"
                                    data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100],
                                            rowStyler:function (index, row) { if (row.is_active === 'Aktif') { return 'font-weight:600' } }">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'department_id',width:100,resizeable:true,sortable:true">Departemen</th>
                                            <th data-options="field:'semester',width:80,resizeable:true,sortable:true,align:'center'">Semester</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div data-options="region:'center'">
                            <div id="menu-act-semester" class="panel-top">
                                <a id="newSemester" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newSemester()">Baru</a>
                                <a id="editSemester" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editSemester()">Ubah</a>
                                <a id="saveSemester" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveSemester()">Simpan</a>
                                <a id="clearSemester" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearSemester()">Batal</a>
                                <a id="deleteSemester" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteSemester()">Hapus</a>
                            </div>
                            <div class="title">
                                <h6><span id="mark-semester"></span>Semester: <span id="title-semester"></span></h6>
                            </div>
                            <div class="p-3" id="page-semester">
                                <form id="form-semester-main" method="post">
                                <input type="hidden" id="id-semester" name="id" value="-1" />
                                <div class="mb-1">
                                    @if (auth()->user()->getDepartment->is_all != 1)
                                        <input value="{{ auth()->user()->getDepartment->name }}" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                        <input type="hidden" name="department_id" value="{{ auth()->user()->department_id }}" />
                                    @else
                                        <select name="department_id" id="SemesterDeptId" class="easyui-combobox" style="width:375px;height:22px;" data-options="label:'<b>*</b>Departemen:',labelWidth:'125px',labelPosition:'before',panelHeight:125">
                                            @foreach ($depts as $dept)
                                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                                <div class="mb-1">
                                    <input name="semester" id="SemesterId" class="easyui-textbox" style="width:200px;height:22px;" data-options="label:'<b>*</b>Semester:',labelWidth:'125px',labelPosition:'before'" />
                                    <span class="mr-2 @if (auth()->user()->getDepartment->is_all != 1) d-none @endif">
                                        <input name="is_all" id="SemesterIdAll" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Semua Departemen',labelWidth:'140px',labelPosition:'after'" />
                                    </span>
                                </div>
                                <div class="mb-1">
                                    <input name="remark" class="easyui-textbox" style="width:375px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',labelPosition:'before',multiline:true" />
                                </div>
                                <div class="mb-1">
                                    <input name="is_active" id="SemesterIsActive" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                                </div>
                                <div class="mb-1" id="SemesterGradeIdDiv">
                                    <select name="grade_id" id="SemesterGradeId" class="easyui-combogrid" style="width:275px;height:22px;" data-options="
                                        label:'<b>*</b>Tingkat:',
                                        labelWidth:'125px',
                                        panelWidth: 230,
                                        idField: 'id',
                                        textField: 'grade',
                                        fitColumns:true,
                                        columns: [[
                                            {field:'department',title:'Departemen',width:150},
                                            {field:'grade',title:'Tingkat',width:80},
                                        ]],
                                    ">
                                    </select>
                                </div>
                                </form>
                                <p class="well mt-5">
                                    <label style="font-weight:500">Penting:</label><br/>
                                    Menu ini mengatur setiap nama semester yang ada di departemen sekolah. Setiap pergantian semester, pengguna harus mengubah status aktif semester ini.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div title="Kelas" class="p-1">
                    <div class="container-fluid mb-2">
                        <div class="row">
                            <div class="col-12 p-0 text-right">
                                <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportAcademicRef('tb-ref-class','{{ url('academic/class/export-') }}','pdf','Kelas')">Ekspor PDF</a>
                            </div>
                        </div>
                    </div>
                    <div class="easyui-layout" style="height:{{ $TabContentHeight }};width:{{ $WindowWidth }};">
                        <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
                            <div class="p-1">
                                <table id="tb-ref-class" class="easyui-datagrid" style="width:100%;height:{{ $TabGridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                                    <thead>
                                        <tr>
                                            @if (auth()->user()->getDepartment->is_all == 1)
                                            <th data-options="field:'department_id',width:100,resizeable:true">Departemen</th>
                                            @endif
                                            <th data-options="field:'schoolyear_id',width:80,resizeable:true,sortable:true,align:'center'">Thn. Ajaran</th>
                                            <th data-options="field:'grade_id',width:70,resizeable:true,sortable:true,align:'center'">Tingkat</th>
                                            <th data-options="field:'class',width:80,resizeable:true,sortable:true">Kelas</th>
                                            <th data-options="field:'quota',width:100,resizeable:true,sortable:false">Kapasitas/Terisi</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                        <div data-options="region:'center'">
                            <div id="menu-act-class" class="panel-top">
                                <a id="newClass" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newClass()">Baru</a>
                                <a id="editClass" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editClass()">Ubah</a>
                                <a id="saveClass" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="saveClass()">Simpan</a>
                                <a id="clearClass" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearClass()">Batal</a>
                                <a id="deleteClass" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deleteClass()">Hapus</a>
                            </div>
                            <div class="title">
                                <h6><span id="mark-class"></span>Kelas: <span id="title-class"></span></h6>
                            </div>
                            <div class="p-3" id="page-class">
                                <form id="form-class-main" method="post">
                                <input type="hidden" id="id-class" name="id" value="-1" />
                                <div class="mb-1">
                                    <input id="ClassDeptId" class="easyui-textbox" style="width:375px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                                    <input id="id-class-department" type="hidden" name="department_id" value="-1" />
                                </div>
                                <div class="mb-1">
                                    <select name="grade_id" id="ClassGradeId" class="easyui-combogrid" style="width:275px;height:22px;" data-options="
                                        label:'<b>*</b>Tingkat:',
                                        labelWidth:'125px',
                                        panelWidth: 230,
                                        idField: 'id',
                                        textField: 'grade',
                                        fitColumns:true,
                                        columns: [[
                                            {field:'department',title:'Departemen',width:150},
                                            {field:'grade',title:'Tingkat',width:80},
                                        ]],
                                    ">
                                    </select>
                                    <span class="mr-2"></span>
                                    <a class="easyui-linkbutton small-btn" onclick="reloadClassGrade()" style="width:27px;height:22px;"><i class="ms-Icon ms-Icon--Refresh"></i></a>
                                </div>
                                <div class="mb-1">
                                    <select name="schoolyear_id" class="easyui-combobox" id="ClassSchoolYearId" style="width:275px;height:22px;" data-options="label:'<b>*</b>Tahun Ajaran:',labelWidth:'125px',panelHeight:78,valueField:'id',textField:'text'">
                                        <option value="">---</option>
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <input name="class" id="ClassId" class="easyui-textbox" style="width:275px;height:22px;" data-options="label:'<b>*</b>Kelas:',labelWidth:'125px',labelPosition:'before'" />
                                    <span class="mr-2"></span>
                                    <input name="is_copy" id="ClassCopy" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Salin dari tahun ajaran sebelumnya',labelWidth:'250px',labelPosition:'after'" />
                                </div>
                                <div class="mb-1">
                                    <select name="employee_id" id="ClassEmployeeId" class="easyui-combogrid" style="width:375px;height:22px;" data-options="
                                        label:'<b>*</b>Wali Kelas:',
                                        labelWidth:'125px',
                                        panelWidth: 440,
                                        idField: 'id',
                                        textField: 'name',
                                        url: '{{ url('hr/combo-grid') }}',
                                        method: 'post',
                                        mode:'remote',
                                        fitColumns:true,
                                        queryParams: { _token: '{{ csrf_token() }}' },
                                        columns: [[
                                            {field:'employee_id',title:'NIP',width:80},
                                            {field:'name',title:'Nama',width:200},
                                            {field:'section',title:'Bagian',width:150},
                                        ]],
                                    ">
                                    </select>
                                </div>
                                <div class="mb-1">
                                    <input name="capacity" id="ClassCapacity" class="easyui-numberspinner" style="width:375px;height:22px;" data-options="label:'<b>*</b>Kapasitas:',labelWidth:'125px',labelPosition:'before',min:1" />
                                </div>
                                <div class="mb-1">
                                    <input name="remark" class="easyui-textbox" style="width:375px;height:50px;" data-options="label:'Keterangan:',labelWidth:'125px',labelPosition:'before',multiline:true" />
                                </div>
                                <div class="mb-1">
                                    <input name="is_active" class="easyui-checkbox" value="2" style="height:22px;" data-options="label:'Non Aktif:',labelWidth:'125px',labelPosition:'before'" />
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    //
    var menuActionGrade = document.getElementById("menu-act-grade").getElementsByTagName("a")
    var markGrade = document.getElementById("mark-grade")
    var titleGrade = document.getElementById("title-grade")
    var idGrade = document.getElementById("id-grade")
    //
    var menuActionSchoolYear = document.getElementById("menu-act-schoolyear").getElementsByTagName("a")
    var markSchoolYear = document.getElementById("mark-schoolyear")
    var titleSchoolYear = document.getElementById("title-schoolyear")
    var idSchoolYear = document.getElementById("id-schoolyear")
    //
    var menuActionSemester = document.getElementById("menu-act-semester").getElementsByTagName("a")
    var markSemester = document.getElementById("mark-semester")
    var titleSemester = document.getElementById("title-semester")
    var idSemester = document.getElementById("id-semester")
    //
    var menuActionClass = document.getElementById("menu-act-class").getElementsByTagName("a")
    var markClass = document.getElementById("mark-class")
    var titleClass = document.getElementById("title-class")
    var idClass = document.getElementById("id-class")
    $(function () {
        // grade
        sessionStorage.formRef_Akademik_Grade = "init"
        var dgGrade = $("#tb-ref-grade")
        dgGrade.datagrid({
            url: "{{ url('academic/grade/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRef_Akademik_Grade == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleGrade.innerText = row.grade
                    actionButtonGrade("active",[2,3])
                    $("#GradeIdAll").checkbox("disable")
                    $("#form-grade-main").form("load", "{{ url('academic/grade/show') }}" + "/" + row.id)
                    $("#page-grade").waitMe("hide")
                }
            }
        })
        dgGrade.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgGrade.datagrid('getPager').pagination())
        actionButtonGrade("{{ $ViewType }}", [])
        $("#GradeId").numberspinner({
            onSpinUp: function() {
                titleGrade.innerText = $(this).val()
            },
            onSpinDown: function() {
                titleGrade.innerText = $(this).val()
            },
        })
        $("#page-grade").waitMe({effect:"none"})
        // school year
        sessionStorage.formRef_Akademik_SchoolYear = "init"
        var dgSchoolYear = $("#tb-ref-schoolyear")
        dgSchoolYear.datagrid({
            url: "{{ url('academic/school-year/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRef_Akademik_SchoolYear == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleSchoolYear.innerText = row.school_year
                    actionButtonSchoolYear("active",[2,3])
                    $("#SchoolYearId").numberspinner("readonly")
                    $("#form-schoolyear-main").form("load", "{{ url('academic/school-year/show') }}" + "/" + row.id)
                    $("#page-schoolyear").waitMe("hide")
                }
            }
        })
        dgSchoolYear.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgSchoolYear.datagrid('getPager').pagination())
        actionButtonSchoolYear("{{ $ViewType }}", [])
        $("#SchoolYearId").numberspinner({
            onSpinUp: function() {
                titleSchoolYear.innerText = $(this).val()
            },
            onSpinDown: function() {
                titleSchoolYear.innerText = $(this).val()
            },
        })
        $("#page-schoolyear").waitMe({effect:"none"})
        // semester
        sessionStorage.formRef_Akademik_Semester = "init"
        var dgSemester = $("#tb-ref-semester")
        dgSemester.datagrid({
            url: "{{ url('academic/semester/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRef_Akademik_Semester == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleSemester.innerText = row.semester
                    actionButtonSemester("active",[2,3])
                    $("#SemesterIdAll").checkbox("disable")
                    $("#form-semester-main").form("load", "{{ url('academic/semester/show') }}" + "/" + row.id)
                    $("#page-semester").waitMe("hide")
                }
            }
        })
        dgSemester.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgSemester.datagrid('getPager').pagination())
        actionButtonSemester("{{ $ViewType }}", [])
        $("#SemesterId").textbox("textbox").bind("keyup", function (e) {
            titleSemester.innerText = $(this).val()
        })
        $("#SemesterIsActive").checkbox({
            onChange: function(checked) {
                if (checked) {
                    $("#SemesterGradeIdDiv").css("display","none")
                } else {
                    $("#SemesterGradeIdDiv").css("display","block")
                }
            }
        })
        $("#page-semester").waitMe({effect:"none"})
        $("#SemesterGradeId").combogrid({
            url: '{{ url('academic/grade/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
        })
        $("#SemesterDeptId").combobox({
            onChange: function(newVal,oldVal) {
                $("#SemesterGradeId").combogrid("grid").datagrid("load",{_token: '{{ csrf_token() }}', department_id: newVal})
            }
        })
        // class
        sessionStorage.formRef_Akademik_Class = "init"
        var dgClass = $("#tb-ref-class")
        dgClass.datagrid({
            url: "{{ url('academic/class/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formRef_Akademik_Class == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titleClass.innerText = row.class
                    actionButtonClass("active",[2,3])
                    $("#form-class-main").form("load", "{{ url('academic/class/show') }}" + "/" + row.id)
                    $("#id-class-department").val(row.id_department)
                    $("#ClassDeptId").textbox("setValue", row.department_id)
                    $("#ClassSchoolYearId").combobox("reload", "{{ url('academic/school-year/combo-box') }}" + "/" + 1 + "?_token=" + "{{ csrf_token() }}").combobox("setValue",row.id_schoolyear)
                    $("#ClassCopy").checkbox("disable")
                    $("#page-class").waitMe("hide")
                }
            }
        })
        dgClass.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgClass.datagrid('getPager').pagination())
        actionButtonClass("{{ $ViewType }}", [])
        $("#ClassId").textbox("textbox").bind("keyup", function (e) {
            titleClass.innerText = $(this).val()
        })
        $("#page-class").waitMe({effect:"none"})
        $("#ClassGradeId").combogrid({
            url: '{{ url('academic/grade/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function (index, row) {
                $("#id-class-department").val(row.department_id)
                $("#ClassDeptId").textbox("setValue", row.department)
                $("#ClassSchoolYearId").combobox("setValue","").combobox("reload", "{{ url('academic/school-year/combo-box') }}" + "/" + 1 + "?_token=" + "{{ csrf_token() }}")
                $("#PlacementProspectiveGroupId").combogrid('hidePanel')
            }
        })
        $("#ClassCopy").checkbox({
            onChange: function(checked) {
                if (checked) {
                    $("#ClassId").textbox("setValue", "-")
                    $("#ClassId").textbox("readonly", true)
                    $("#ClassEmployeeId").combogrid("setValue", 1)
                    $("#ClassEmployeeId").combogrid("readonly", true)
                    $("#ClassCapacity").numberspinner("setValue", 1)
                    $("#ClassCapacity").numberspinner("readonly", true)
                } else {
                    $("#ClassId").textbox("setValue", "")
                    $("#ClassId").textbox("readonly", false)
                    $("#ClassEmployeeId").combogrid("setValue", "")
                    $("#ClassEmployeeId").combogrid("readonly", false)
                    $("#ClassCapacity").numberspinner("setValue", "")
                    $("#ClassCapacity").numberspinner("readonly", false)
                }
            }
        })
    })
    // grade
    function newGrade() {
        sessionStorage.formRef_Akademik_Grade = "active"
        $("#form-grade-main").form("reset")
        actionButtonGrade("active", [0,1,4])
        markGrade.innerText = "*"
        titleGrade.innerText = ""
        idGrade.value = "-1"
        $("#GradeIdAll").checkbox("enable")
        $("#page-grade").waitMe("hide")
    }
    function editGrade() {
        sessionStorage.formRef_Akademik_Grade = "active"
        markGrade.innerText = "*"
        actionButtonGrade("active", [0, 1, 4])
    }
    function saveGrade() {
        if (sessionStorage.formRef_Akademik_Grade == "active") {
            ajaxAcademicRef("form-grade-main", "tb-ref-grade", "academic/grade/store", "grade")
        }
    }
    function deleteGrade() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Tingkat terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                destroyAcademicRef("tb-ref-grade", "{{ url('academic/grade/destroy') }}"+"/"+idGrade.value, "grade")
            }
        })
    }
    function clearGrade() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearGrade()
            }
        })
    }
    function actionClearGrade() {
        sessionStorage.formRef_Akademik_Grade = "init"
        $("#form-grade-main").form("reset")
        actionButtonGrade("init", [])
        titleGrade.innerText = ""
        markGrade.innerText = ""
        idGrade.value = "-1"
        $("#page-grade").waitMe({effect:"none"})
    }
    function actionButtonGrade(viewType, idxArray) {
        for (var i = 0; i < menuActionGrade.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionGrade[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionGrade[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionGrade[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionGrade[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    // school year
    function newSchoolYear() {
        sessionStorage.formRef_Akademik_SchoolYear = "active"
        $("#form-schoolyear-main").form("reset")
        actionButtonSchoolYear("active", [0,1,4])
        markSchoolYear.innerText = "*"
        titleSchoolYear.innerText = ""
        idSchoolYear.value = "-1"
        $("#SchoolYearId").numberspinner("readonly", false)
        $("#page-schoolyear").waitMe("hide")
    }
    function editSchoolYear() {
        sessionStorage.formRef_Akademik_SchoolYear = "active"
        markSchoolYear.innerText = "*"
        actionButtonSchoolYear("active", [0, 1, 4])
    }
    function saveSchoolYear() {
        if (sessionStorage.formRef_Akademik_SchoolYear == "active") {
            ajaxAcademicRef("form-schoolyear-main", "tb-ref-schoolyear", "academic/school-year/store", "schoolyear")
        }
    }
    function deleteSchoolYear() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Tingkat terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                destroyAcademicRef("tb-ref-schoolyear", "{{ url('academic/school-year/destroy') }}"+"/"+idSchoolYear.value, "schoolyear")
            }
        })
    }
    function clearSchoolYear() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearSchoolYear()
            }
        })
    }
    function actionClearSchoolYear() {
        sessionStorage.formRef_Akademik_SchoolYear = "init"
        $("#form-schoolyear-main").form("reset")
        actionButtonSchoolYear("init", [])
        titleSchoolYear.innerText = ""
        markSchoolYear.innerText = ""
        idSchoolYear.value = "-1"
        $("#page-schoolyear").waitMe({effect:"none"})
    }
    function actionButtonSchoolYear(viewType, idxArray) {
        for (var i = 0; i < menuActionSchoolYear.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionSchoolYear[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionSchoolYear[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionSchoolYear[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionSchoolYear[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    // semester
    function newSemester() {
        sessionStorage.formRef_Akademik_Semester = "active"
        $("#form-semester-main").form("reset")
        actionButtonSemester("active", [0,1,4])
        markSemester.innerText = "*"
        titleSemester.innerText = ""
        idSemester.value = "-1"
        $("#SemesterIdAll").checkbox("enable")
        $("#SemesterGradeIdDiv").css("display","none")
        $("#page-semester").waitMe("hide")
    }
    function editSemester() {
        sessionStorage.formRef_Akademik_Semester = "active"
        markSemester.innerText = "*"
        actionButtonSemester("active", [0, 1, 4])
    }
    function saveSemester() {
        if (sessionStorage.formRef_Akademik_Semester == "active") {
            ajaxAcademicRef("form-semester-main", "tb-ref-semester", "academic/semester/store", "semester")
        }
    }
    function deleteSemester() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Semester terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                destroyAcademicRef("tb-ref-semester", "{{ url('academic/semester/destroy') }}"+"/"+idSemester.value, "semester")
            }
        })
    }
    function clearSemester() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearSemester()
            }
        })
    }
    function actionClearSemester() {
        sessionStorage.formRef_Akademik_Semester = "init"
        $("#form-semester-main").form("reset")
        actionButtonSemester("init", [])
        titleSemester.innerText = ""
        markSemester.innerText = ""
        idSemester.value = "-1"
        $("#SemesterGradeIdDiv").css("display","block")
        $("#page-semester").waitMe({effect:"none"})
    }
    function actionButtonSemester(viewType, idxArray) {
        for (var i = 0; i < menuActionSemester.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionSemester[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionSemester[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionSemester[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionSemester[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    // class
    function newClass() {
        sessionStorage.formRef_Akademik_Class = "active"
        $("#form-class-main").form("reset")
        actionButtonClass("active", [0,1,4])
        markClass.innerText = "*"
        titleClass.innerText = ""
        idClass.value = "-1"
        $("#ClassCopy").checkbox("enable")
        $("#page-class").waitMe("hide")
    }
    function editClass() {
        sessionStorage.formRef_Akademik_Class = "active"
        markClass.innerText = "*"
        actionButtonClass("active", [0, 1, 4])
    }
    function saveClass() {
        if (sessionStorage.formRef_Akademik_Class == "active") {
            ajaxAcademicRef("form-class-main", "tb-ref-class", "academic/class/store", "class")
        }
    }
    function deleteClass() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Kelas terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                destroyAcademicRef("tb-ref-class", "{{ url('academic/class/destroy') }}"+"/"+idClass.value, "class")
            }
        })
    }
    function clearClass() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearClass()
            }
        })
    }
    function actionClearClass() {
        sessionStorage.formRef_Akademik_Class = "init"
        $("#form-class-main").form("reset")
        actionButtonClass("init", [])
        titleClass.innerText = ""
        markClass.innerText = ""
        idClass.value = "-1"
        $("#ClassCopy").checkbox("disable")
        $("#page-class").waitMe({effect:"none"})
    }
    function actionButtonClass(viewType, idxArray) {
        for (var i = 0; i < menuActionClass.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionClass[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionClass[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionClass[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionClass[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function reloadClassGrade() {
        $("#ClassGradeId").combogrid().datagrid("grid").reload()
    }
    // common used
    function exportAcademicRef(idGrid, route, document, title) {
        var dg = $("#"+idGrid).datagrid('getData')
        if (dg.total > 0) {
            exportDocument(route + document,dg.rows,"Ekspor data "+title+" ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function ajaxAcademicRef(idForm, idGrid, route, subject) {
        $("#"+idForm).ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}' },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-"+subject).waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxRefResponse(response, idGrid, subject)
                $("#page-"+subject).waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-"+subject).waitMe("hide")
            }
        })
        return false
    }
    function destroyAcademicRef(idGrid, url, subject) {
        $.post(url, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
            ajaxRefResponse(response, idGrid, subject)
        }).fail(function(xhr) {
            failResponse(xhr)
        })
    }
    function ajaxRefResponse(response, idGrid, subject) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            switch(subject) {
                case "grade":
                    actionClearGrade()
                    break;
                case "schoolyear":
                    actionClearSchoolYear()
                    break;
                case "semester":
                    actionClearSemester()
                    break;
                case "class":
                    actionClearClass()
                    break;
                default:
                    actionClearGeneration()
            }
            $("#"+idGrid).datagrid("reload")
        } else {
            $.messager.alert('Peringatan', response.message, 'error')
        }
    }
</script>
