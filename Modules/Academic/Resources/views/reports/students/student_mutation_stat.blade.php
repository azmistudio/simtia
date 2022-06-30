@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-12">
            <label class="mb-1" style="width:200px;">Departemen:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:150px;">Angkatan:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:80px;">Periode dari:</label>
            <span class="mr-2"></span>
            <label class="mb-1" style="width:80px;">sampai:</label>
        </div>
        <div class="col-12 mb-2">
            <form id="form-student-mutation-stat">
                <input type="hidden" id="id-student-mutation-stat-dept" />
                <div class="mb-1">
                    <select id="AcademicReportDept" class="easyui-combobox cbox" style="width:200px;height:22px;" data-options="panelHeight:125">
                        @if (auth()->user()->getDepartment->is_all != 1)
                            <option value="{{ auth()->user()->department_id }}">{{ auth()->user()->getDepartment->name }}</option>
                        @else 
                            @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span class="mr-2"></span>
                    <select id="AcademicReportSchoolYear" class="easyui-combogrid cgrd" style="width:150px;height:22px;" data-options="
                        panelWidth: 450,
                        idField: 'id',
                        textField: 'school_year',
                        url: '{{ url('academic/school-year/combo-grid') }}',
                        queryParams: { _token: '{{ csrf_token() }}' },
                        mode:'remote',
                        fitColumns:true,
                        pagination:true,
                        columns: [[
                            {field:'school_year',title:'Angkatan',width:100,align:'center',sortable:true},
                            {field:'remark',title:'Keterangan',width:200},
                        ]],
                    ">
                    </select>
                    <span class="mr-2"></span>
                    <input type="text" id="AcademicReportPeriodStart" class="easyui-textbox tbox" style="width:80px;height:22px;" readonly="readonly" />
                    <span class="mr-2"></span>
                    <select id="AcademicReportPeriodEnd" class="easyui-combobox cbox" style="width:80px;height:22px;" data-options="panelHeight:50,valueField:'value',textField:'text'"></select>
                    <span class="mr-2"></span>
                    <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterStudentMutationStat(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                    <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterStudentMutationStat(0);$('#form-student-mutation-stat').form('reset')" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </div>
            </form>
        </div>
        <div class="col-7">
            <table id="tb-report-student-mutation-stat" class="easyui-datagrid cgrid" style="width:100%;height:{{ $GridHeight }}"
                data-options="method:'post',toolbar:'#toolbarAcademicReportStudentMutation'">
                <thead>
                    <tr>
                        <th data-options="field:'mutation',width:440,resizeable:true">Jenis Mutasi</th>
                        <th data-options="field:'total',width:80,resizeable:true,align:'center'">Jumlah</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="col-5">
            <div class="text-center pt-2" id="bar-student-mutation-stat" style="height: {{ $GridHeight }};width: 100%;border: solid 1px #d5d5d5;"></div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportStudentMutation">
    <div class="container-fluid">
        <div class="row">
            <div class="col-8">
                <span style="line-height: 25px;"><b>Data Mutasi Santri</b></span>
            </div>
            <div class="col-4 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportStudentMutationStat('pdf')">Ekspor PDF</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportSchoolYear").combogrid({
            onClickRow: function(index, row) {
                $("#AcademicReportPeriodStart").textbox("setText", row.school_year)
                $("#AcademicReportPeriodEnd").combobox("loadData", [{ value: row.school_year, text:row.school_year },{ value: parseInt(row.school_year) + 1, text: parseInt(row.school_year) + 1}])
            }
        })
        $('#tb-report-student-mutation-stat').datagrid({
            view: detailview,
            detailFormatter:function(index,row){
                return '<div style="padding:2px;position:relative;"><table class="ddv"></table></div>';
            },
            onExpandRow: function(index,row){
                var ddv = $(this).datagrid('getRowDetail',index).find('table.ddv')
                ddv.datagrid({
                    url: "{{ url('academic/report/student/mutation/stat/data/detail') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + row.department_id + "&start=" + row.start + "&end=" + row.end + "&mutation_id=" + row.id,
                    fitColumns:true,
                    rownumbers:true,
                    loadMsg:'',
                    height:'auto',
                    columns:[[
                        {field:'department',title:'Departemen',width:100},
                        {field:'student_no',title:'NIS',width:100,align:'center'},
                        {field:'student_id',title:'Santri',width:150},
                        {field:'mutation_date',title:'Tanggal',width:100,align:'center'},
                        {field:'remark',title:'Keterangan',width:150,resizeable:true}
                    ]],
                    onResize:function(){
                        $('#tb-report-student-mutation-stat').datagrid('fixDetailRowHeight',index)
                    },
                    onLoadSuccess:function(){
                        setTimeout(function(){
                            $('#tb-report-student-mutation-stat').datagrid('fixDetailRowHeight',index)
                        },0);
                    }
                })
                $('#tb-report-student-mutation-stat').datagrid('fixDetailRowHeight',index)
            }
        })
    })
    function filterStudentMutationStat(val) {
        if (val > 0) {
            $('#tb-report-student-mutation-stat').datagrid("reload", "{{ url('academic/report/student/mutation/stat/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&department_id=" + $("#AcademicReportDept").textbox("getValue") + "&start=" + $("#AcademicReportPeriodStart").textbox("getText") + "&end=" + $("#AcademicReportPeriodEnd").combobox("getValue"))
            $.post("{{ url('academic/report/student/mutation/stat/graph') }}", { 
                _token: "{{ csrf_token() }}",  
                department: $("#AcademicReportDept").textbox("getText"),
                department_id: $("#AcademicReportDept").textbox("getValue"),
                start: $("#AcademicReportPeriodStart").textbox("getText"),
                end: $("#AcademicReportPeriodEnd").combobox("getValue"),
            }, function (response) {
                $("#bar-student-mutation-stat").html(response)
            })
        } else {
            $("#AcademicReportPeriodEnd").combobox("loadData", [])
            $('#tb-report-student-mutation-stat').datagrid("loadData", [])
            $("#bar-student-mutation-stat").html("")
        }
    }
    function exportAcademicReportStudentMutationStat(doctype) {
        var dg = $("#tb-report-student-mutation-stat").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getText"),
                department_id: $("#AcademicReportDept").textbox("getValue"),
                start: $("#AcademicReportPeriodStart").textbox("getText"),
                end: $("#AcademicReportPeriodEnd").combobox("getValue"),
                rows: dg.rows
            }
            exportDocument("{{ url('academic/report/student/mutation/stat/export-') }}" + doctype,payload,"Ekspor Mutasi Santri ke PDF","{{ csrf_token() }}")
        }
    }
</script>