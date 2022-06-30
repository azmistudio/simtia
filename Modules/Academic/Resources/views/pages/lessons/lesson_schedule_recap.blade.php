@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 351 . "px";
@endphp
<div class="container-fluid mt-1 mb-1">
    <div class="row">
        <div class="col-8 p-0">
            <h5><i class="ms-Icon ms-Icon--FlickLeft"></i> Rekapitulasi Jadwal Guru</h5>
        </div>
        <div class="col-4 p-0 text-right">
            <a class="easyui-linkbutton top-btn" data-options="iconCls:'ms-Icon ms-Icon--PDF'" onclick="exportScheduleRecap('pdf')">Ekspor PDF</a>
        </div>
    </div>
</div>
<div class="easyui-layout" style="height:{{ $WindowHeight }};width:{{ $WindowWidth }};">
    <div data-options="region:'west'">
        <div class="pb-3">
            <div class="title">
                <h6><span id="mark-schedule-recap"></span>Periode: <span id="title-schedule-recap"></span></h6>
            </div>
            <form id="ff-schedule-recap" method="post">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <br/>
                            <div class="mb-1">
                                <input id="fdept-schedule-recap" class="easyui-textbox" style="width:308px;height:22px;" data-options="label:'Departemen:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <input id="fschoolyear-schedule-recap" class="easyui-textbox" style="width:308px;height:22px;" data-options="label:'Tahun Ajaran:',labelWidth:'125px',readonly:true" />
                            </div>
                            <div class="mb-1">
                                <select id="fschedule-schedule-recap" class="easyui-combogrid" style="width:308px;height:22px;" data-options="
                                    label:'Info Jadwal:',
                                    labelWidth:'125px',
                                    panelWidth: 570,
                                    idField: 'id',
                                    textField: 'description',
                                    fitColumns:true,
                                    pagination:true,
                                    columns: [[
                                        {field:'department',title:'Departemen',width:150},
                                        {field:'school_year',title:'Thn. Ajaran',width:100},
                                        {field:'description',title:'Info Jadwal',width:170},
                                        {field:'start_date',title:'Periode Awal',width:100},
                                        {field:'end_date',title:'Periode Akhir',width:100},
                                    ]],
                                ">
                                </select>
                            </div>
                            <div class="mb-1" style="margin-left:125px;padding:5px 0">
                                <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="filterScheduleRecap({fschedule: $('#fschedule-schedule-recap').combogrid('getValue')})">Cari</a>
                                <a href="javascript:void(0)" class="easyui-linkbutton small-btn flist-box" onclick="$('#ff-schedule-recap').form('reset');filterScheduleRecap({});$('#title-schedule-recap').text('')">Batal</a>
                            </div>
                            <div>
                                <table id="tb-schedule-recap" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                                       data-options="method:'post',rownumbers:'true',pagination:'true',pageSize:50,pageList:[10,25,50,75,100]">
                                    <thead>
                                        <tr>
                                            <th data-options="field:'employee_no',width:100,resizeable:true,sortable:true,align:'center'">NIP</th>
                                            <th data-options="field:'employee',width:240,resizeable:true,sortable:true">Nama</th>
                                            <th data-options="field:'teaching',width:100,align:'center'">Mengajar</th>
                                            <th data-options="field:'assist',width:100,align:'center'">Asistensi</th>
                                            <th data-options="field:'addition',width:100,align:'center'">Tambahan</th>
                                            <th data-options="field:'time',width:100,align:'center'">Jam</th>
                                            <th data-options="field:'class',width:150,align:'center'">Kelas</th>
                                            <th data-options="field:'day',width:100,align:'center'">Hari</th>
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
    var dgScheduleRecap = $("#tb-schedule-recap")
    $(function () {
        $("#fschedule-schedule-recap").combogrid('grid').datagrid({
            url: '{{ url('academic/lesson/schedule/info/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#title-schedule-recap").text(row.start_date + " s.d " + row.end_date)
                $("#fdept-schedule-recap").textbox('setValue', row.department)
                $("#fschoolyear-schedule-recap").textbox('setValue', row.school_year)
                $("#fschedule-schedule-recap").combogrid('hidePanel')
            }
        })
        dgScheduleRecap.datagrid({
            url: "{{ url('academic/lesson/schedule/recap/data') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
        })
    })
    function filterScheduleRecap(params) {
        if (Object.keys(params).length > 0) {
            dgScheduleRecap.datagrid("load", { params, _token: "{{ csrf_token() }}" })
        } else {
            dgScheduleRecap.datagrid("load", { _token: "{{ csrf_token() }}" })
        }
    }
    function exportScheduleRecap(document) {
        var dg = $("#tb-schedule-recap").datagrid('getData')
        if (dg.total > 0) {
            $.messager.progress({ title: 'Ekspor Data Rekapitulasi Jadwal Guru', msg:'Mohon tunggu...' });
            $.post("{{ url('academic/lesson/schedule/recap/export-') }}" + document, $.param({ 
                _token: "{{ csrf_token() }}", 
                data: JSON.stringify(dg.rows),
                period: $("#title-schedule-recap").text(),
                department: $("#fdept-schedule-recap").textbox('getValue'),
                school_year: $("#fschoolyear-schedule-recap").textbox('getValue'),
                schedule: $("#fschedule-schedule-recap").combogrid('getText'),
            }, true), function(response) {
                $.messager.progress('close')
                window.open("/storage/downloads/" + response)
            })
        }
    }
</script>