@inject('scoreval', 'Modules\Academic\Repositories\Exam\ExamReportEloquent')
@php
	$tot_lesson = count($lessons);
	$arr_lesson = collect($lessons)->toArray();
	$arr_score_asp = array();
	foreach ($score_aspects as $aspect) 
	{
		$arr_score_asp[] = array($aspect->basis, $aspect->remark, $aspect->id);
	}
	$tot_score_asp = count($arr_score_asp);
	$span_lesson = 3 + 2 * $tot_score_asp + 1;
	$sum_avg_student = 0;
	$jsum_avg_student = 0;
	$avg_lesson = array();
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
					<th class="subtitle" align="center" rowspan="2">No.</th>
					<th class="subtitle" align="center" rowspan="2">NIS</th>
					<th class="subtitle" align="center" rowspan="2">Nama</th>
					@for ($i = 0; $i < $tot_score_asp; $i++)
					<th class="subtitle" align="center" colspan="2">{{ $arr_score_asp[$i][1] }}</th>
					@endfor
					<th class="subtitle" align="center" rowspan="2">Rata - Rata Santri</th>
				</tr>
				<tr style="background-color:#CCFFFF;">
					@for ($i = 0; $i < $tot_score_asp; $i++)
					<th class="subtitle" align="center">Nilai Angka</th>
					<th class="subtitle" align="center">Nilai Huruf</th>
					@endfor
				</tr>
			</thead>
			<tbody>
				@for ($i = 0; $i < $tot_score_asp; $i++)
				@php
					$lesson_id = $arr_lesson[$i]['id'];
					$lesson_name = $arr_lesson[$i]['lesson'];
					for ($j = 0; $j < $tot_score_asp; $j++)
					{
						$avg_lesson[] = array(0, 0);
					}
					$tot_avg_student = 0;
					$jtot_avg_student = 0;
					$no = 1;
				@endphp
				<tr>
					<td class="" colspan="{{ $span_lesson }}" style="background-color:#efefef;"><b>{{ $lesson_name }}</b></td>
				</tr>
				@foreach ($students as $student)
				<tr>
					<td class="text-center">{{ $no }}</td>
					<td class="text-center">{{ $student->student_no }}</td>
					<td>{{ $student->student }}</td>
					@for ($k = 0; $k < $tot_score_asp; $k++)
					@php 
						$values = $scoreval->reportLeggerLessonData($arr_lesson[$i]['id'], $requests->class_id, $requests->semester_id, $student->id, $arr_score_asp[$k][2]);
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
				@php $no++; @endphp
				@endforeach
				@php $valsumavg = $jsum_avg_student == 0 ? "" : round($sum_avg_student / $jsum_avg_student, 2); @endphp
				<tr>
					<td class="text-right" colspan="3"><b>RATA - RATA {{ $lesson_name }}</b></td>
					@for ($l = 0; $l < $tot_score_asp; $l++)
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
				@endfor
			</tbody>
		</table>
    </body>
</html>