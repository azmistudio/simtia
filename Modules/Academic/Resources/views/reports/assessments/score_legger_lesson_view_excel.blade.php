@inject('scoreval', 'Modules\Academic\Repositories\Exam\ExamReportEloquent')
@php
	$score_asp_arr = array();
	foreach ($score_aspects as $row) 
	{
		$score_asp_arr[] = array($row->basis, $row->remark, $row->id);
	}
	$count_aspect = count($score_asp_arr);
	$avg_lesson = array();
	for ($i=0; $i < $count_aspect; $i++) 
	{ 
		$avg_lesson[] = array(0,0);
	}
	$span_lesson = 3 + 2 * $count_aspect + 1;
	$tot_avg_student = 0;
	$jtot_avg_student = 0;
	$sum_avg_student = 0;
	$jsum_avg_student = 0;
	$no = 1;
@endphp
<html>
    <head>
        <title>LAPORAN LEGGER PELAJARAN</title>
        <style type="text/css">
            body { margin: 0; padding: 0; font-family: "Calibri", "Open Sans", serif !important; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            table.no-border, table.no-border th, table.no-border td { border: none; }
            table { border-collapse: collapse; border: 1px solid #000; }
            th, td { border-top: 1px solid #000; }
            .title { font-size: 13pt; font-weight: bold; }
            .subtitle { font-size: 11pt; font-weight: bold; }
        </style>
    </head>
    <body>
    	<table class="no-border">
    		<tbody>
    			<tr>
    				<td colspan="{{ $span_lesson }}" align="center" class="title">{{ strtoupper(Session::get('institute')) }}</td>
    			</tr>
    			<tr>
    				<td colspan="{{ $span_lesson }}" align="center" class="title">LAPORAN LEGGER NILAI RAPOR PELAJARAN</td>
    			</tr>
    			<tr>
    				<td colspan="{{ $span_lesson }}" align="center" class="subtitle">DEPARTEMEN {{ $requests->department }} - TAHUN AJARAN {{ $requests->schoolyear }} - KELAS {{ $requests->class }} - SEMESTER {{ $requests->semester }}</td>
    			</tr>
    		</tbody>
    	</table>
    	<br/>
		<table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;">
			<thead>
				<tr style="background-color:#CCFFFF;">
					<th class="text-center" rowspan="2">No.</th>
					<th class="text-center" rowspan="2">NIS</th>
					<th class="text-center" rowspan="2">Nama</th>
					@for ($i = 0; $i < $count_aspect; $i++)
					<th class="text-center" colspan="2">{{ $score_asp_arr[$i][1] }}</th>
					@endfor
					<th class="text-center" rowspan="2">Rata - Rata Santri</th>
				</tr>
				<tr style="background-color:#CCFFFF;">
					@for ($i = 0; $i < $count_aspect; $i++)
					<th class="text-center">Nilai Angka</th>
					<th class="text-center">Nilai Huruf</th>
					@endfor
				</tr>
			</thead>
			<tbody>
				@foreach ($students as $student)
				<tr>
					<td class="text-center">{{ $no++ }}</td>
					<td class="text-center">{{ $student->student_no }}</td>
					<td>{{ $student->student }}</td>
					@for ($k = 0; $k < $count_aspect; $k++)
					@php 
						$values = $scoreval->reportLeggerLessonData($requests->lesson_id, $requests->class_id, $requests->semester_id, $student->id, $score_asp_arr[$k][2]);
						$tot_avg_student += $values[0]->value;
						$jtot_avg_student += 1;
						$avg_lesson[$k][0] += $values[0]->value;
						$avg_lesson[$k][1] += 1;
						$avg_score = $jtot_avg_student == 0 ? '' : round($tot_avg_student/$jtot_avg_student, 2);
					@endphp
					<td class="text-center">{{ number_format($values[0]->value,2) }}</td>
					<td class="text-center">{{ $values[0]->value_letter }}</td>
					@endfor
					<td class="text-center">{{ number_format($avg_score,2) }}</td>
					@if ($jtot_avg_student != 0)
					@php
						$sum_avg_student += $avg_score;
						$jsum_avg_student += 1;
					@endphp
					@endif
				</tr>
				@endforeach
				@php $valsumavg = $jsum_avg_student == 0 ? "" : round($sum_avg_student / $jsum_avg_student, 2); @endphp
				<tr>
					<td class="text-right" colspan="3"><b>RATA - RATA</b></td>
					@for ($l = 0; $l < $count_aspect; $l++)
					@php
						$sum_avg_lesson = $avg_lesson[$l][0];
						$jsum_avg_lesson = $avg_lesson[$l][1];
						$valavglesson = $jsum_avg_lesson == 0 ? '' : round($sum_avg_lesson/$jsum_avg_lesson, 2);
					@endphp
					<td class="text-center"><b>{{ number_format($valavglesson,2) }}</b></td>
					<td></td>
					@endfor
					<td class="text-center"><b>{{ number_format($valsumavg,2) }}</b></td>
				</tr>
			</tbody>
		</table>
    </body>
</html>