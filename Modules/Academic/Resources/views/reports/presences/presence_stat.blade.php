@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:130px;">Tahun Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Semester:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:150px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-stat">
                <input type="hidden" id="id-report-presence-stat-deptid" />
                <select id="AcademicReportSchoolyear" class="easyui-combobox cbox" style="width:130px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'">
                    @foreach ($schoolyears as $schoolyear)
                    <option value="{{ $schoolyear->id }}">{{ $schoolyear->is_active == 1 ? $schoolyear->school_year .' (A)' : $schoolyear->school_year }}</option>
                    @endforeach
                </select>
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:150px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select name="semester_id" id="AcademicReportSemester" class="easyui-combogrid cgrd" style="width:100px;height:22px;" data-options="
                    panelWidth: 250,
                    idField: 'id',
                    textField: 'semester',
                    fitColumns: true,
                    method: 'post',
                    columns: [[
                        {field:'department',title:'Departemen',width:150},
                        {field:'semester',title:'Semester',width:100},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <select id="AcademicReportGrade" class="easyui-combobox cbox" style="width:110px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'"></select>
                <span class="mr-2"></span>
                <select id="AcademicReportClass" class="easyui-combobox cbox" style="width:150px;height:22px;" data-options="panelHeight:125,valueField:'value',textField:'text'"></select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportPresenceStat(1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-stat').form('reset');filterAcademicReportPresenceStat(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-12">
                <br/>
                <table id="tb-report-presence-stat" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',rownumbers:true,toolbar:'#toolbarAcademicReportPresenceStat'">
                    <thead>
                        <tr>
                            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'">NIS</th>
                            <th data-options="field:'student',width:200,resizeable:true">Nama</th>
                            <th data-options="field:'graph',width:650,resizeable:true"></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportPresenceStat">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Statistik Kehadiran Santri</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportPresenceStat('pdf')">Ekspor PDF</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportSemester").combogrid({
            url: '{{ url('academic/semester/combo-grid') }}',
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-stat-deptid").val(row.department_id)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").combobox("reload", "{{ url('academic/report/grade/combo-box') }}" + "/" + row.department_id + "/0?_token=" + "{{ csrf_token() }}")
                $("#AcademicReportClass").combobox({ readonly : true })
            }
        })
        $("#tb-report-presence-stat").datagrid({
            onDblClickRow: function(index, row) {
                var payload = {
                    department: $("#AcademicReportDept").textbox("getValue"),
                    schoolyear: $("#AcademicReportSchoolyear").combobox("getValue"),
                    grade: $("#AcademicReportGrade").combobox("getText"),
                    class: $("#AcademicReportClass").combobox("getText"),
                    studentno: row.student_no,
                    student: row.student,
                    student_id: row.student_id,
                    class_id: $("#AcademicReportClass").combobox("getValue"),
                    start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                    end_date: $("#AcademicReportDateTo").datebox("getValue"),
                }
                exportDocument("{{ url('academic/report/presence/daily/export-') }}" + "pdf",payload,"Ekspor data Presensi Harian ke PDF","{{ csrf_token() }}")
            }
        })
        $("#AcademicReportGrade").combobox({
            onSelect: function(record) {
                if (record.value > 0) {
                    $("#AcademicReportClass").combobox({ readonly : false })
                    $("#AcademicReportClass").combobox("reload", "{{ url('academic/report/class/combo-box') }}" + "/" + record.value + "/" + $("#AcademicReportSchoolyear").combobox("getValue") + "/0?_token=" + "{{ csrf_token() }}")
                } else {
                    $("#AcademicReportClass").combobox({ readonly : true })            
                }
            }
        })
        $("#AcademicReportClass").combobox({ readonly : true })
    })
    function filterAcademicReportPresenceStat(val) {
        if (val > 0) {
            if ($("#AcademicReportGrade").combobox("getValue") > 0 && $("#AcademicReportClass").combobox("getValue") > 0) {
                $("#tb-report-presence-stat").datagrid("reload", "{{ url('academic/report/presence/stat/data') }}" 
                    + "?_token=" + "{{ csrf_token() }}" 
                    + "&department_id=" + $("#id-report-presence-stat-deptid").val()
                    + "&schoolyear_id=" + $("#AcademicReportSchoolyear").combobox("getValue")
                    + "&semester_id=" + $("#AcademicReportSemester").combogrid("getValue")
                    + "&grade_id=" + $("#AcademicReportGrade").combobox("getValue")
                    + "&class_id=" + $("#AcademicReportClass").combobox("getValue")
                    + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
                    + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
                )
            }
        } else {
            $("#tb-report-presence-stat").datagrid("loadData", [])
        }
    }
    function exportAcademicReportPresenceStat(document) {
        var dg = $("#tb-report-presence-stat").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                department_id: $("#id-report-presence-stat-deptid").val(),
                schoolyear: $("#AcademicReportSchoolyear").combobox("getText"),
                schoolyear_id: $("#AcademicReportSchoolyear").combobox("getValue"),
                semester: $("#AcademicReportSemester").combogrid("getText"),
                semester_id: $("#AcademicReportSemester").combogrid("getValue"),
                grade: $("#AcademicReportGrade").combobox("getText"),
                grade_id: $("#AcademicReportGrade").combobox("getValue"),
                class: $("#AcademicReportClass").combobox("getText"),
                class_id: $("#AcademicReportClass").combobox("getValue"),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/stat/export-') }}" + document,payload,"Ekspor Statistik Presensi Harian ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>