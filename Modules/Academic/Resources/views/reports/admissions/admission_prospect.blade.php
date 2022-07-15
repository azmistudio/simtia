@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 278 . "px";
    $PanelHeight = $InnerHeight - 271 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:110px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:220px;">Proses:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Kelompok Penerimaan:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Nama Santri:</label>
            </div>
            <div class="col-12">
                <form id="form-academic-report-prospect-student">
                <input type="hidden" id="id-academic-report-prospect-department" value="-1" /> 
                <input id="AcademicReportDept" class="easyui-textbox tbox" style="width:110px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input id="AcademicReportProcess" class="easyui-textbox tbox" style="width:220px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select id="AcademicReportProspectGroup" class="easyui-combogrid cgrd" style="width:200px;height:22px;" data-options="
                    panelWidth: 570,
                    idField: 'id',
                    textField: 'group',
                    fitColumns:true,
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'admission_id',title:'Proses',width:200},
                        {field:'group',title:'Kelompok',width:110},
                        {field:'quota',title:'Kapasitas/Terisi',width:100},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportStudent" class="easyui-textbox tbox" style="width:220px;height:22px;"/>
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportProspect(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-academic-report-prospect-student').form('reset');filterAcademicReportProspect(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12 mt-2">
                <table id="tb-academic-report-prospect-student" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,singleSelect:true,showFooter:true,pagination:true,toolbar:'#toolbarAcademicReportProspectStudent',pageSize:50,pageList:[10,25,50,75,100]">
                    <thead>
                        <tr>
                            <th data-options="field:'department',width:110,align:'center'">Departemen</th>
                            <th data-options="field:'admission',width:180,align:'center'">Proses Penerimaan</th>
                            <th data-options="field:'prospect_group',width:120,resizeable:true,align:'center'">Kelompok</th>
                            <th data-options="field:'registration_no',width:110,sortable:true">No.Daftar</th>
                            <th data-options="field:'name',width:180,sortable:true">Santri</th>
                            <th data-options="field:'gender',width:80">Gender</th>
                            <th data-options="field:'class',width:110">Tingkat/Kelas</th>
                            <th data-options="field:'status',width:110">Status</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportProspectStudent">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Calon Santri</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--Print" plain="true" onclick="printAcademicReportProspect()">Cetak</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportProspect('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportProspect('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-academic-report-prospect-student").datagrid()
        $("#AcademicReportProspectGroup").combogrid('grid').datagrid({
            url: '{{ url('academic/admission/prospective-group/combo-grid') }}',
            method: 'post',
            mode:'remote',
            queryParams: { _token: '{{ csrf_token() }}' },
            onClickRow: function(index, row) {
                $("#AcademicReportProcess").textbox('setText', row.admission_id)
                $("#AcademicReportDept").textbox('setText', row.department)
                $("#AcademicReportProspectGroup").combogrid('hidePanel')
                $("#id-report-prospect-group-department").val(row.department_id)
            }
        })
    })
    function filterAcademicReportProspect(val) {
        if (val > 0) {
            if ($("#AcademicReportProspectGroup").combogrid("getValue") > 0) {
                $("#tb-academic-report-prospect-student").datagrid("reload", "{{ url('academic/report/admission/prospect/data') }}" 
                    + "?_token=" + "{{ csrf_token() }}" 
                    + "&prospect_group_id=" + $("#AcademicReportProspectGroup").combogrid("getValue") 
                    + "&prospect_group=" + $("#AcademicReportProspectGroup").combogrid("getText") 
                    + "&department_id=" + $("#id-academic-report-prospect-department").val()
                    + "&department=" + $("#AcademicReportDept").combogrid("getText") 
                    + "&student=" + $("#AcademicReportStudent").textbox("getText") 
                )
            }
        } else {
            $("#tb-academic-report-prospect-student").datagrid("loadData", [])
        }
    }
    function printAcademicReportProspect() {
        var dg = $("#tb-academic-report-prospect-student").datagrid("getSelected")
        if (dg !== null) {
            exportDocument("{{ url('academic/admission/prospective-student/print') }}", { id: dg.id }, "Ekspor data ke PDF", "{{ csrf_token() }}")
        } else {
            $.messager.alert('Peringatan', 'Pilih salah satu Santri.', 'warning')
        }
    }
    function exportAcademicReportProspect(document) {
        var arrays = []
        var dg = $("#tb-academic-report-prospect-student").datagrid('getData')
        if (dg.total > 0) {
            for (var i = 0; i < dg.rows.length; i++) {
                arrays.push(dg.rows[i].department_id)
            }
            exportDocument("{{ url('academic/admission/prospective-student/export-') }}" + document,dg.rows,"Ekspor data Calon Santri ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>