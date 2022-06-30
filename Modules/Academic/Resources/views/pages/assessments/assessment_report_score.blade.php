@php
    $GridHeight = $requests['h'] - 360 . "px";
    $i = 0; 
    $exams = array();
@endphp
<table id="tb-assessment-report-score" class="easyui-datagrid" style="width:100%;height:{{ $GridHeight }}" data-options="method:'post',rownumbers:'true',showFooter:'true'">
    <thead>
        <tr>
            <th data-options="field:'student_no',width:90,resizeable:true,align:'center'">NIS</th>
            <th data-options="field:'student',width:160,resizeable:true">Nama</th>
            @foreach ($assessments as $assessment)
                @php $exams[$i++] = array($assessment['exam_id']); @endphp
                <th data-options="field:'{{ strtolower($assessment['code']) }}',width:130,resizeable:true,align:'center'">{{ $assessment['code'] }} ({{ $assessment['value'] }})</th>
            @endforeach
            <th data-options="field:'value',width:60,resizeable:true,align:'center',editor:{type:'numberbox',options:{precision:2}}">Angka</th>
            <th data-options="field:'value_letter',width:60,resizeable:true,align:'center',editor:{type:'combobox',options:{valueField:'id',textField:'text',panelHeight:78,data:values}}">Huruf</th>
        </tr>
    </thead>
</table>
<script type="text/javascript">
    var exams = []
    @foreach ($exams as $value)
        exams.push({{ $value[0] }})
    @endforeach
    var values = []
    @foreach ($value_letters as $letter)
        values.push({
            id: "{{ $letter['grade'] }}",
            text: "{{ $letter['grade'] }}"
        })
    @endforeach
    $(function () {
        var dg = $("#tb-assessment-report-score")
        dg.datagrid({
            url: "{{ url('academic/assessment/report/formula/score/data') }}",
            queryParams: { 
                _token: "{{ csrf_token() }}", 
                data: exams, 
                class_id: {{ $requests['class_id'] }}, 
                semester_id: {{ $requests['semester_id'] }}, 
                lesson_id: {{ $requests['lesson_id'] }}, 
                score_aspect: {{ $requests['score_aspect'] }}, 
                exam_report_id: {{ $requests['exam_report_id'] }}, 
                employee_id: {{ $requests['employee_id'] }}, 
                grade_id: {{ $requests['grade_id'] }}, 
            },
        })
        dg.datagrid('enableCellEditing').datagrid("enableFilter")
    })
</script>