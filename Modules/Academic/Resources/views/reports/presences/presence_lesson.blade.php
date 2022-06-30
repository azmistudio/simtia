@php
    $WindowHeight = $InnerHeight - 168 . "px";
    $WindowWidth = $InnerWidth - 12 . "px";
    $GridHeight = $InnerHeight - 288 . "px";
@endphp
<div style="overflow-y: auto;">
    <div class="container-fluid mt-1 mb-1">
        <div class="row">
            <div class="col-12">
                <label class="mb-1" style="width:132px;">Departemen:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:70px;">Tingkat:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:100px;">Thn.Ajaran:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:120px;">Kelas:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:170px;">Santri:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Dari Tanggal:</label>
                <span class="mr-2"></span>
                <label class="mb-1" style="width:110px;">Sampai Tanggal:</label>
            </div>
            <div class="col-12">
                <form id="form-report-presence-lesson">
                <input type="hidden" name="student_no" id="id-report-presence-lesson-studentno" />
                <input class="easyui-textbox tbox" id="AcademicReportDept" style="width:132px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportGrade" style="width:70px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportSchoolYear" style="width:100px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <input class="easyui-textbox tbox" id="AcademicReportClass" style="width:120px;height:22px;" data-options="readonly:true" />
                <span class="mr-2"></span>
                <select name="student_id" id="AcademicReportStudent" class="easyui-combogrid cgrd" style="width:170px;height:22px;" data-options="
                    panelWidth: 760,
                    idField: 'id',
                    textField: 'student',
                    fitColumns: true,
                    method: 'post',
                    pagination: true,
                    pageSize:50,
                    pageList:[10,25,50,75,100],
                    columns: [[
                        {field:'department',title:'Departemen',width:120},
                        {field:'grade',title:'Tingkat',width:60,align:'center'},
                        {field:'school_year',title:'Thn. Ajaran',width:100,align:'center'},
                        {field:'class',title:'Kelas',width:120},
                        {field:'student_no',title:'NIS',width:110,align:'center',sortable:true},
                        {field:'student',title:'Nama',width:200,sortable:true},
                    ]],
                ">
                </select>
                <span class="mr-2"></span>
                <input id="AcademicReportDateFrom" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d', strtotime('-1 months')) }}" />
                <span class="mr-2"></span>
                <input id="AcademicReportDateTo" class="easyui-datebox dbox" style="width:110px;height:22px;" data-options="formatter:dateFormatter,parser:dateParser" value="{{ date('Y-m-d') }}" />
                <span class="mr-2"></span>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="filterAcademicReportLessonPresence()" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Search'"></a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" onclick="$('#form-report-presence-lesson').form('reset');filterAcademicReportLessonPresence(-1)" style="height:22px;" data-options="iconCls:'ms-Icon ms-Icon--Clear'"></a>
                </form>
            </div>
            <div class="col-9">
                <br/>
                <table id="tb-report-presence-lesson" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}"
                    data-options="method:'post',showFooter:true,rownumbers:true,pagination:true,pageSize:50,pageList:[10,25,50,75,100],toolbar:'#toolbarAcademicReportLessonPresence'">
                    <thead>
                        <tr>
                            <th data-options="field:'date',width:80,sortable:true,align:'center'">Tanggal</th>
                            <th data-options="field:'time',width:80,resizeable:true,align:'center'">Jam</th>
                            <th data-options="field:'class',width:120,resizeable:true">Kelas</th>
                            <th data-options="field:'remark',width:150,align:'center'">Catatan</th>
                            <th data-options="field:'lesson',width:150,align:'center'">Pelajaran</th>
                            <th data-options="field:'employee',width:150,align:'center'">Guru</th>
                            <th data-options="field:'subject',width:200">Materi</th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div class="col-3" style="padding-top: 12px;">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <td width="65%"><b>Jumlah Kehadiran</b></td>
                            <td>: <b><span id="sum_present"></span></b></td>
                        </tr>
                        <tr>
                            <td><b>Jumlah Ketidakhadiran</b></td>
                            <td>: <b><span id="sum_absent"></span></b></td>
                        </tr>
                        <tr>
                            <td><b>Jumlah Seharusnya</b></td>
                            <td>: <b><span id="sum_required"></span></b></td>
                        </tr>
                        <tr>
                            <td><b>Persentase Kehadiran</b></td>
                            <td>: <b><span id="sum_percent"></span></b></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
{{-- toolbar --}}
<div id="toolbarAcademicReportLessonPresence">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Kehadiran Santri</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportLessonPresence('pdf')">Ekspor PDF</a>
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportLessonPresence('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#AcademicReportStudent").combogrid({
            url: "{{ url('academic/student/combo-grid') }}",
            queryParams: { _token: "{{ csrf_token() }}" },
            onClickRow: function(index, row) {
                $("#id-report-presence-lesson-classid").val(row.class_id)
                $("#id-report-presence-lesson-studentno").val(row.student_no)
                $("#AcademicReportDept").textbox("setValue", row.department)
                $("#AcademicReportGrade").textbox("setValue", row.grade)
                $("#AcademicReportSchoolYear").textbox("setValue", row.school_year)
                $("#AcademicReportClass").textbox("setValue", row.class)
            }
        })
        $("#tb-report-presence-lesson").datagrid()
    })
    function filterAcademicReportLessonPresence() {
        $("#tb-report-presence-lesson").datagrid("reload", "{{ url('academic/report/presence/lesson/data') }}" 
            + "?_token=" + "{{ csrf_token() }}" 
            + "&student_id=" + $("#AcademicReportStudent").combogrid("getValue")
            + "&start_date=" + $("#AcademicReportDateFrom").datebox("getValue")
            + "&end_date=" + $("#AcademicReportDateTo").datebox("getValue")
        )
        $.post("{{ url('academic/report/presence/lesson/data/info') }}", { _token: "{{ csrf_token() }}", student_id: $("#AcademicReportStudent").combogrid("getValue"), start_date: $("#AcademicReportDateFrom").datebox("getValue"), end_date: $("#AcademicReportDateTo").datebox("getValue") }, function(response){
            $("#sum_present").text(response.present)
            $("#sum_absent").text(response.absent)
            $("#sum_required").text(response.total)
            $("#sum_percent").text(response.percent + "%") 
        }, "json")
    }
    function exportAcademicReportLessonPresence(document) {
        var dg = $("#tb-report-presence-lesson").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: $("#AcademicReportDept").textbox("getValue"),
                schoolyear: $("#AcademicReportSchoolYear").textbox("getValue"),
                grade: $("#AcademicReportGrade").textbox("getValue"),
                studentno: $("#id-report-presence-lesson-studentno").val(),
                student: $("#AcademicReportStudent").combogrid("getText"),
                student_id: $("#AcademicReportStudent").combogrid("getValue"),
                start_date: $("#AcademicReportDateFrom").datebox("getValue"), 
                end_date: $("#AcademicReportDateTo").datebox("getValue"),
                sum_present: $("#sum_present").text(),
                sum_absent: $("#sum_absent").text(),
                sum_required: $("#sum_required").text(),
                sum_percent: $("#sum_percent").text(),
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/presence/lesson/export-') }}" + document,payload,"Ekspor Presensi Pelajaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>