@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 275 . "px";
    $SubGridHeight = $InnerHeight - 316 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Presensi Harian</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportPresenceDaily('pdf')">Cetak Form Presensi Harian</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west',split:true,collapsible:true,title:'Daftar'" style="width:300px">
        <div class="p-1">
            <form id="ff-presence-daily" method="post" class="mb-1">
            @csrf
                <div class="mb-1">
                    <select name="class_id" id="fclass-presence-daily" class="easyui-combogrid" style="width:285px;height:22px;" data-options="
                        label:'Kelas:',
                        labelWidth:100,
                        panelWidth: 570,
                        idField: 'id',
                        textField: 'class',
                        url: '{{ url('academic/class/student/combo-grid') }}',
                        method: 'post',
                        mode:'remote',
                        fitColumns:true,
                        queryParams: { _token: '{{ csrf_token() }}' },
                        columns: [[
                            {field:'department',title:'Departemen',width:150},
                            {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                            {field:'grade',title:'Tingkat',width:65,align:'center'},
                            {field:'class',title:'Kelas',width:120},
                            {field:'semester',title:'Semester',width:80,align:'center'},
                            {field:'capacity',title:'Kapasitas/Terisi',width:120},
                        ]],
                    ">
                    </select>
                </div>
                <div style="margin-left:100px;padding:5px 0">
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterPresenceDaily({fclass: $('#fclass-presence-daily').combobox('getValue')})">Cari</a>
                    <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-presence-daily').form('reset');filterPresenceDaily({})">Batal</a>
                </div>
            </form>
            <table id="tb-presence-daily" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="singleSelect:true,method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                <thead>
                    <tr>
                        <th data-options="field:'class_id',width:80,resizeable:true,sortable:true">Kelas</th>
                        <th data-options="field:'period',width:180,resizeable:true">Periode</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <div data-options="region:'center'">
        <div id="menu-act-presence-daily" class="panel-top">
            <a id="newPresenceDaily" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Add'" onclick="newPresenceDaily()">Baru</a>
            <a id="editPresenceDaily" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Edit'" onclick="editPresenceDaily()">Ubah</a>
            <a id="savePresenceDaily" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Save'" onclick="savePresenceDaily()">Simpan</a>
            <a id="clearPresenceDaily" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Clear'" onclick="clearPresenceDaily()">Batal</a>
            <a id="deletePresenceDaily" class="easyui-linkbutton action-btn" style="width: 80px" data-options="plain:true,iconCls:'ms-Icon ms-Icon--Delete'" onclick="deletePresenceDaily()">Hapus</a>
            <a id="pdfPresenceDaily" class="easyui-linkbutton action-btn" data-options="plain:true,iconCls:'ms-Icon ms-Icon--PDF'" onclick="pdfPresenceDaily()">Cetak</a>
        </div>
        <div class="title">
            <h6><span id="mark-presence-daily"></span>Presensi: <span id="title-presence-daily"></span></h6>
        </div>
        <div class="pt-3 pb-3" id="page-presence-daily">
            <form id="form-presence-daily-main" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-5">
                            <input type="hidden" id="id-presence-daily" name="id" value="-1" />
                            <input type="hidden" id="id-presence-daily-semester" name="semester_id" value="-1" />
                            <input type="hidden" id="id-presence-daily-start" name="start" value="" />
                            <input type="hidden" id="id-presence-daily-end" name="end" value="" />
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceDailyDept" style="width:380px;height:22px;" data-options="label:'Departemen:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceDailyGrade" style="width:380px;height:22px;" data-options="label:'Tingkat:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceDailySchoolYear" style="width:380px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceDailySemester" style="width:380px;height:22px;" data-options="label:'Semester:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input class="easyui-textbox" id="PresenceDailyPeriod" style="width:380px;height:22px;" data-options="label:'Periode:',labelWidth:'170px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select name="class_id" id="PresenceDailyClass" class="easyui-combogrid" style="width:380px;height:22px;" data-options="
                                    label:'<b>*</b>Kelas:',
                                    labelWidth:'170px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'class',
                                    fitColumns:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                                        {field:'grade',title:'Tingkat',width:65,align:'center'},
                                        {field:'class',title:'Kelas',width:120},
                                        {field:'semester',title:'Semester',width:80,align:'center'},
                                        {field:'capacity',title:'Kapasitas/Terisi',width:120},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1">
                                <input name="start_date" id="PresenceDailyStart" class="easyui-datebox" style="width:280px;height:22px;" data-options="label:'<b>*</b>Tanggal Awal:',labelWidth:'170px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <input name="end_date" id="PresenceDailyEnd" class="easyui-datebox" style="width:280px;height:22px;" data-options="label:'<b>*</b>Tanggal Akhir:',labelWidth:'170px',formatter:dateFormatter,parser:dateParser" />
                            </div>
                            <div class="mb-1">
                                <select name="active_day" id="PresenceDailyDay" class="easyui-combobox" style="width:230px;height:22px;" data-options="label:'<b>*</b>Jumlah Hari Aktif Belajar:',labelWidth:'170px',labelPosition:'before',panelHeight:112,valueField:'id',textField:'name'"></select>
                            </div>
                        </div>
                        <div class="col-7">
                            <div class="mb-1">
                                <input class="easyui-numberspinner" id="PresenceDailyBulk" style="width:135px;height:22px;" value="0" data-options="label:'Isi Masal:',labelWidth:'80px',min:0" />
                                <span class="mr-1"></span>
                                <select id="PresenceDailyBulkValue" class="easyui-combobox" style="width:100px;height:22px;" data-options="panelHeight:112,valueField:'id',textField:'name'">
                                    <option value="present">Hadir</option>
                                    <option value="permit">Ijin</option>
                                    <option value="sick">Sakit</option>
                                    <option value="absent">Alpa</option>
                                    <option value="leave">Cuti</option>
                                </select>
                                <span class="mr-1"></span>
                                <a href="javascript:void(0)" class="easyui-linkbutton small-btn" onclick="bulkSet()" style="width:80px;height:22px;">Terapkan</a>
                            </div>
                            <div class="mb-1">
                                <table id="tb-presence-daily-form" class="easyui-datagrid" style="width:100%;height:{{ $SubGridHeight }}"
                                    data-options="method:'post',rownumbers:'true', queryParams: { _token: '{{ csrf_token() }}' }">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'id',width:50,hidden:true">ID</th>
                                            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'">NIS</th>
                                            <th data-options="field:'name',width:150,resizeable:true,align:'left'">Nama</th>
                                            <th data-options="field:'present',width:50,resizeable:true,align:'center',editor:'numberbox'">Hadir</th>
                                            <th data-options="field:'permit',width:50,resizeable:true,align:'center',editor:'numberbox'">Ijin</th>
                                            <th data-options="field:'sick',width:50,resizeable:true,align:'center',editor:'numberbox'">Sakit</th>
                                            <th data-options="field:'absent',width:50,resizeable:true,align:'center',editor:'numberbox'">Alpa</th>
                                            <th data-options="field:'leave',width:50,resizeable:true,align:'center',editor:'numberbox'">Cuti</th>
                                            <th data-options="field:'remark',width:200,resizeable:true,align:'left',editor:'text'">Keterangan</th>
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
{{-- dialog --}}
<div id="presence-daily-form-w" class="easyui-window" title="Cetak Form Presensi Harian" data-options="modal:true,closed:true,minimizable:false,maximizable:false,collapsible:false,iconCls:'ms-Icon ms-Icon--Print'" style="width:385px;padding:10px;">
    <form id="form-presence-daily-form">
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceDailyFormDept" style="width:350px;height:22px;" data-options="label:'Departemen:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceDailyFormGrade" style="width:350px;height:22px;" data-options="label:'Tingkat:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceDailyFormSchoolYear" style="width:350px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceDailyFormSemester" style="width:350px;height:22px;" data-options="label:'Semester:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <input class="easyui-textbox" id="presenceDailyFormPeriod" style="width:350px;height:22px;" data-options="label:'Periode:',labelWidth:'150px',readonly:true" />
        </div>
        <div class="mb-1">
            <select id="presenceDailyFormClass" class="easyui-combogrid" style="width:350px;height:22px;" data-options="
                label:'<b>*</b>Kelas:',
                labelWidth:'150px',
                panelWidth: 570,
                idField: 'id',
                textField: 'class',
                fitColumns:true,
                columns: [[
                    {field:'department',title:'Departemen',width:150},
                    {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                    {field:'grade',title:'Tingkat',width:80,align:'center'},
                    {field:'class',title:'Kelas',width:120},
                    {field:'capacity',title:'Kapasitas/Terisi',width:120},
                ]],
            ">
            </select>
        </div>
    </form>
    <div style="margin-left:150px;padding:5px 0">
        <a href="javascript:void(0)" class="easyui-linkbutton small-btn" onclick="printPresentDailyForm($('#presenceDailyFormClass').combobox('getValue'))" style="height:22px;">Cetak Form</a>
    </div>
</div>
<script type="text/javascript">
    var menuActionPresenceDaily = document.getElementById("menu-act-presence-daily").getElementsByTagName("a")
    var titlePresenceDaily = document.getElementById("title-presence-daily")
    var markPresenceDaily = document.getElementById("mark-presence-daily")
    var idPresenceDaily = document.getElementById("id-presence-daily")
    var dgPresenceDaily = $("#tb-presence-daily")
    $(function () {
        sessionStorage.formPresensi_Harian = "init"
        dgPresenceDaily.datagrid({
            url: "{{ url('academic/presence/daily/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onDblClickRow: function (index, row) {
                if (sessionStorage.formPresensi_Harian == "active") {
                    $.messager.alert('Peringatan', 'Form sedang aktif, silahkan batalkan terlebih dahulu', 'error')
                } else {
                    titlePresenceDaily.innerText = row.period
                    actionButtonPresenceDaily("active",[2,3])
                    $("#form-presence-daily-main").form("load", "{{ url('academic/presence/daily/show') }}" + "/" + row.id)
                    $("#page-presence-daily").waitMe("hide")
                }
            }
        })
        dgPresenceDaily.datagrid("getPager").pagination({layout: ["list","first","prev","next","last","info"]})
        pagingGrid(dgPresenceDaily.datagrid('getPager').pagination())
        actionButtonPresenceDaily("{{ $ViewType }}", [])
        $("#PresenceDailyClass").combogrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#PresenceDailyDept").textbox('setValue', row.department)
                $("#PresenceDailyGrade").textbox('setValue', row.grade)
                $("#PresenceDailySchoolYear").textbox('setValue', row.school_year)
                $("#PresenceDailySemester").textbox('setValue', row.semester)
                let periods = row.period.slice(1,-1).split("|")
                $("#PresenceDailyPeriod").textbox('setValue', parsingDate(periods[0]) + " s.d " + parsingDate(periods[1]))
                $("#id-presence-daily-semester").val(row.semester_id)
                $("#id-presence-daily-start").val(periods[0])
                $("#id-presence-daily-end").val(periods[1])
                var starts = periods[0].split("-")
                var ends = periods[1].split("-")
                var now = new Date()
                var d1 = new Date(starts[0], parseInt(starts[1]) - 1, starts[2])
                var d2 = new Date(ends[0], parseInt(ends[1]) - 1, ends[2])
                $("#PresenceDailyStart").datebox().datebox('calendar').calendar({
                    validator: function(date){
                        return d1 <= date && date <= d2
                    }
                })
                $("#PresenceDailyEnd").datebox().datebox('calendar').calendar({
                    validator: function(date){
                        return d1 <= date && date <= d2;
                    }
                })
                $("#PresenceDailyClass").combogrid('hidePanel')
                $("#tb-presence-daily-form").datagrid("load", "{{ url('academic/student/list') }}" + "?fclass=" + row.id)
            }
        })
        $("#PresenceDailyEnd").datebox({
            onSelect: function(date){
                let start_val = $("#PresenceDailyStart").datebox("getValue")
                if (start_val != "") {
                    let params = start_val.split("/")
                    getTotalDays(parseInt(params[0]), date.getDate())
                }
            }
        })
        $("#tb-presence-daily-form").datagrid('enableCellEditing').datagrid('gotoCell',{
            index: 1,
            field: 'present'
        })
        $("#form-presence-daily-main").form({
            onLoadSuccess: function(data) {
                $("#id-presence-daily-semester").val(data.semester_id)
                $("#id-presence-daily-start").val(data.period_start)
                $("#id-presence-daily-end").val(data.period_end)
                $("#PresenceDailyDept").textbox('setValue', data.department)
                $("#PresenceDailyGrade").textbox('setValue', data.grade)
                $("#PresenceDailySchoolYear").textbox('setValue', data.school_year)
                $("#PresenceDailySemester").textbox('setValue', data.semester)
                $("#PresenceDailyPeriod").textbox('setValue', data.period)
                $("#PresenceDailyStart").datebox("setValue", data.start_date)
                $("#PresenceDailyEnd").datebox("setValue", data.end_date)
                let starts = data.start_date.split("-")
                let ends = data.end_date.split("-")
                getTotalDays(parseInt(starts[2]), parseInt(ends[2]))
                $("#tb-presence-daily-form").datagrid("load", "{{ url('academic/presence/daily/list') }}" + "?id=" + data.id)
                $("#PresenceDailyDay").combobox("setValue", data.active_day)
                $("#PresenceDailyClass").combogrid("readonly", true)
            }
        })
        $("#presenceDailyFormClass").combogrid('grid').datagrid({
            url: '{{ url('academic/class/student/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#presenceDailyFormDept").textbox('setValue', row.department)
                $("#presenceDailyFormGrade").textbox('setValue', row.grade)
                $("#presenceDailyFormSchoolYear").textbox('setValue', row.school_year)
                $("#presenceDailyFormSemester").textbox('setValue', row.semester)
                let periods = row.period.slice(1,-1).split("|")
                $("#presenceDailyFormPeriod").textbox('setValue', parsingDate(periods[0]) + " s.d " + parsingDate(periods[1]))
                $("#presenceDailyFormClass").combogrid('hidePanel')
            }
        })
        $("#page-presence-daily").waitMe({effect:"hide"})
    })
    function filterPresenceDaily(params) {
        if (Object.keys(params).length > 0) {
            dgPresenceDaily.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgPresenceDaily.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function newPresenceDaily() {
        sessionStorage.formPresensi_Harian = "active"
        $("#form-presence-daily-main").form("reset")
        actionButtonPresenceDaily("active", [0,1,4,5])
        markPresenceDaily.innerText = "*"
        titlePresenceDaily.innerText = ""
        idPresenceDaily.value = "-1"
        $("#id-presence-daily-semester").val("-1")
        $("#tb-presence-daily-form").datagrid("loadData", [])
        $("#PresenceDailyClass").combogrid("readonly", false)
        $("#page-presence-daily").waitMe("hide")
    }
    function editPresenceDaily() {
        sessionStorage.formPresensi_Harian = "active"
        markPresenceDaily.innerText = "*"
        actionButtonPresenceDaily("active", [0,1,4])
    }
    function savePresenceDaily() {
        if (sessionStorage.formPresensi_Harian == "active") {
            ajaxPresenceDaily("academic/presence/daily/store")
        }
    }
    function deletePresenceDaily() {
        $.messager.confirm("Konfirmasi", "Anda akan menghapus data Presensi Harian terpilih, tetap lanjutkan?", function (r) {
            if (r) {
                $.post("{{ url('academic/presence/daily/destroy') }}" +"/"+idPresenceDaily.value, { _token: "{{ csrf_token() }}" }, "json").done(function( response ) {
                    ajaxPresenceDailyResponse(response)
                }).fail(function(xhr) {
                    failResponse(xhr)
                })
            }
        })
    }
    function ajaxPresenceDaily(route) {
        var dg = $("#tb-presence-daily-form").datagrid('getData')
        $("#form-presence-daily-main").ajaxSubmit({
            url: route,
            data: { _token: '{{ csrf_token() }}', students: dg.rows },
            beforeSubmit: function(formData, jqForm, options) {
                $("#page-presence-daily").waitMe({effect:"facebook"})
            },
            success: function(response) {
                ajaxPresenceDailyResponse(response)
                $("#page-presence-daily").waitMe("hide")
            },
            error: function(xhr) {
                failResponse(xhr)
                $("#page-presence-daily").waitMe("hide")
            }
        })
        return false
    }
    function ajaxPresenceDailyResponse(response) {
        if (response.success) {
            Toast.fire({icon:"success",title:response.message})
            actionClearPresenceDaily()
            $("#tb-presence-daily").datagrid("reload")
        } else {
            showError(response)
        }
    }
    function clearPresenceDaily() {
        $.messager.confirm("Konfirmasi", "Perubahan belum disimpan, tetap batalkan?", function (r) {
            if (r) {
                actionClearPresenceDaily()
            }
        })
    }
    function actionButtonPresenceDaily(viewType, idxArray) {
        for (var i = 0; i < menuActionPresenceDaily.length; i++) {
            if (viewType == "init") {
                $("#" + menuActionPresenceDaily[i].id).linkbutton({ disabled: false })
                if (i > 0) {
                    $("#" + menuActionPresenceDaily[i].id).linkbutton({disabled: true})
                }
            } else {
                $("#" + menuActionPresenceDaily[i].id).linkbutton({disabled: false})
                for (var j = 0; j < idxArray.length; j++) {
                    $("#" + menuActionPresenceDaily[idxArray[j]].id).linkbutton({ disabled: true })
                }
            }
        }
    }
    function actionClearPresenceDaily() {
        sessionStorage.formPresensi_Harian = "init"
        $("#form-presence-daily-main").form("reset")
        actionButtonPresenceDaily("init", [])
        titlePresenceDaily.innerText = ""
        markPresenceDaily.innerText = ""
        idPresenceDaily.value = "-1"
        $("#id-presence-daily-semester").val("-1")
        $("#tb-presence-daily-form").datagrid("loadData", [])
        $("#PresenceDailyClass").combogrid("readonly", false)
        $("#page-presence-daily").waitMe({effect:"hide"})
    }
    function exportPresenceDaily(document) {
        var dg = $("#tb-presence-daily").datagrid('getData')
        if (dg.total > 0) {
            exportDocument("{{ url('academic/presence/daily/export-') }}" + document,dg.rows,"Ekspor data Presensi Harian ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
    function getDaysInMonth(month, year) {
        let day = new Date()
        let days = new Date(year, month, 0).getDate()
        var dayDiff = []
        for (var i = 1; i <= days; i++) {
            dayDiff.push({id: i, name: i})
        }
        $("#PresenceDailyStart").combobox('loadData', dayDiff)
        $("#PresenceDailyStart").combobox('setValue', day.getDate())
        $("#PresenceDailyEnd").combobox('loadData', dayDiff)
        $("#PresenceDailyEnd").combobox('setValue', day.getDate())
    }
    function getTotalDays(start, end) {
        var totals = []
        for (var i = 1; i <= (end - start) + 1; i++) {
            totals.push({id: i, name: i})
        }
        $("#PresenceDailyDay").combobox('loadData', totals)
        $("#PresenceDailyDay").combobox('setValue', (end - start) + 1)
    }
    function bulkSet() {
        var dg = $('#tb-presence-daily-form')
        let value = $("#PresenceDailyBulk").numberbox('getValue')
        let field = $("#PresenceDailyBulkValue").combobox('getValue')
        let total = dg.datagrid('getData').total
        var rows = {}
        rows[field] = value
        for (var i = 0; i < total; i++) {
            dg.datagrid('updateRow',{
                index: i,
                row: rows
            })
        }
    }
    function pdfPresenceDaily() {
        if (idPresenceDaily.value != -1) {
            exportDocument("{{ url('academic/presence/daily/print') }}", { id: idPresenceDaily.value }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        }
    }
    function exportPresenceDaily() {
        $("#presence-daily-form-w").window("open")
    }
    function printPresentDailyForm(id) {
        if (id != '') {
            exportDocument("{{ url('academic/presence/daily/print/form') }}", { id: id }, "Cetak Form Presensi Harian", "{{ csrf_token() }}")
            $("#presence-daily-form-w").window("close")
        }
    }
</script>
