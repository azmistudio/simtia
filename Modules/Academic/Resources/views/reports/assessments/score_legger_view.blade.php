@php
    $PageHeight = intval(str_replace('px', '', $requests['height'])) - 23 . "px";
    $dates = array();
    $cols = array();
    $count = 0;
    foreach ($exams as $exam)
    {
        foreach ($exam_dates as $date)
        {
            if ($exam->lesson_exam_id == $date->lesson_exam_id)
            {
                $dates[] = array($exam->lesson_exam_id,$date->id,Carbon\Carbon::createFromFormat('Y-m-d',$date->date)->format('d/m/Y'));
                $count = array_count_values(array_column($dates,0));
            }
        }
        $cols[] = array($exam->code, $exam->subject, $exam->lesson_exam_id, $count);
    }
@endphp
<table id="tb-score-legger-data" class="easyui-datagrid" style="width:100%;height:{{ $PageHeight }}"
    data-options="method:'post',rownumbers:true,toolbar:'#toolbarAcademicReportScoreLegger'">
    <thead>
        <tr>
            <th data-options="field:'student_no',width:80,resizeable:true,align:'center'" rowspan="2">NIS</th>
            <th data-options="field:'student',width:180,resizeable:true" rowspan="2">Nama</th>
            @for ($i = 0; $i < count($cols); $i++)
            <th data-options="field:'{{ $cols[$i][0] }}',align:'center'" colspan="{{ $cols[$i][3][$cols[$i][2]] }}">{{ strtoupper($cols[$i][1]) }}</th>
            @endfor
        </tr>
        <tr>
            @for ($i = 0; $i < count($dates); $i++)
            <th data-options="field:'{{ '_'.$dates[$i][1] }}',align:'center'">{{ $dates[$i][2] }}</th>
            @endfor
        </tr>
    </thead>
</table>
{{-- toolbar --}}
<div id="toolbarAcademicReportScoreLegger">
    <div class="container-fluid">
        <div class="row">
            <div class="col-3">
                <span style="line-height: 25px;"><b>Data Legger Nilai</b></span>
            </div>
            <div class="col-9 text-right">
                <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportDailyPresence('excel')">Ekspor Excel</a>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $("#tb-score-legger-data").datagrid("reload", "{{ url('academic/report/assessment/score/legger/data') }}" + "?_token=" + "{{ csrf_token() }}" + "&lesson_id=" + {{ $requests['lesson_id'] }} + "&class_id=" + {{ $requests['class_id'] }} + "&semester_id=" + {{ $requests['semester_id'] }})
    })
    function exportAcademicReportDailyPresence(document) {
        var dg = $("#tb-score-legger-data").datagrid('getData')
        if (dg.total > 0) {
            var payload = {
                department: "{{ $requests['department'] }}",
                schoolyear: "{{ $requests['schoolyear'] }}",
                class: "{{ $requests['class'] }}",
                lesson: "{{ $requests['lesson'] }}",
                semester: "{{ $requests['semester'] }}",
                class_id: {{ $requests['class_id'] }},
                lesson_id: {{ $requests['lesson_id'] }},
                semester_id: {{ $requests['semester_id'] }},
                rows: dg.rows, 
            }
            exportDocument("{{ url('academic/report/assessment/score/legger/export-') }}" + document,payload,"Ekspor data Legger Nilai ke "+ document.toUpperCase(),"{{ csrf_token() }}")
        }
    }
</script>