@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:142px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:70px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Thn.Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:80px;">Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:200px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-daily-class">
                <input type="hidden" id="id-report-presence-daily-classid" value="-1" />
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:142px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportGrade" style="width:70px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSchoolYear" style="width:100px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSemester" style="width:80px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select name="class_id" id="AcademicReportClass" class="easyui-combogrid cgrd" style="width:200px;height:22px;" data-options="
                    panelWidth: 570,
                    idField: 'seq',
                    textField: 'class',
                    fitColumns: true,
                    method: 'post',
                    columns: [[
                        {field:'department',title:'Departemen',width:150},
                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                        {field:'grade',title:'Tingkat',width:80,align:'center'},
                        {field:'semester',title:'Semester',width:100},
                        {field:'class',title:'Kelas',width:120},
                        {field:'capacity',title:'Kapasitas/Terisi',width:130},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportDailyPresenceClass()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-daily-class').form('reset');filterAcademicReportDailyPresenceClass(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-daily-class" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,pagination:true,pageSize:50,pageList:[10,25,50,75,100],toolbar:'#toolbarAcademicReportDailyPresenceClass'">
                    <thead>
                        <tr>
                            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'">NIS</th>
                            <th data-options="field:'student',width:150,resizeable:true">Nama</th>
                            <th data-options="field:'present',width:80,align:'center'">Hadir</th>
                            <th data-options="field:'permit',width:80,align:'center'">Ijin</th>
                            <th data-options="field:'sick',width:80,align:'center'">Sakit</th>
                            <th data-options="field:'absent',width:80,align:'center'">Alpa</th>
                            <th data-options="field:'leave',width:80,align:'center'">Cuti</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportDailyPresenceClass">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Presensi Harian per Kelas</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportDailyPresenceClass('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportDailyPresenceClass('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportClass").combogrid({
            url: '{{ url('academic/class/combo-grid/view') }}',
            queryParams: { _token: "{{ csrf_token() }}", fstu_active: 1, fsem_active: 1, fcount: 1 },
            onClickRow: function(index, row) {
                $("#id-report-presence-daily-classid").val(row.id)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").textbox("setValue", row.grade)
                $("#AcademicReportSchoolYear").textbox("setValue", row.school_year)
                $("#AcademicReportSemester").textbox("setValue", row.semester)                    
            }
        })
        $("#tb-report-presence-daily-class").datagrid()
    })
    function filterAcademicReportDailyPresenceClass() {
        $("#tb-report-presence-daily-class").datagrid("reload", "{{ url('academic/report/presence/daily/class/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&class_id=" + $("#id-report-presence-daily-classid").val()
            + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
        )
    }
    function exportAcademicReportDailyPresenceClass(document) {
        var dg = $("#tb-report-presence-daily-class").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                schoolyear: $("#AcademicReportSchoolYear").textbox("getValue"),
                grade: $("#AcademicReportGrade").textbox("getValue"),
                class: $("#AcademicReportClass").combogrid("getText"),
                class_id: $("#id-report-presence-daily-classid").val(),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/daily/class/export-') }}" + document,payload,"Ekspor Presensi Harian Kelas ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>