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
	$avg_score = 0;
@endphp
<div class="container-fluid">
	<div class="row">
		<div class="col-12 text-right p-1">
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportLessonAll('excel')">Ekspor Excel</a>
		</div>
		<div class="col-12 p-1">
			<table class="table table-bordered table-sm" width="100%">
				<thead>
					<tr>
						<th class="text-center" rowspan="2">No.</th>
						<th class="text-center" rowspan="2">NIS</th>
						<th class="text-center" rowspan="2">Nama</th>
						@for ($i = 0; $i < $tot_score_asp; $i++)
						<th class="text-center" colspan="2">{{ $arr_score_asp[$i][1] }}</th>
						@endfor
						<th class="text-center" rowspan="2">Rata - Rata Santri</th>
					</tr>
					<tr>
						@for ($i = 0; $i < $tot_score_asp; $i++)
						<th class="text-center">Nilai Angka</th>
						<th class="text-center">Nilai Huruf</th>
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
						<td colspan="{{ $span_lesson }}" style="background-color:#efefef;"><b>{{ $lesson_name }}</b></td>
					</tr>
					@foreach ($students as $student)
					<tr>
						<td class="text-center">{{ $no }}</td>
						<td class="text-center">{{ $student->student_no }}</td>
						<td>{{ $student->student }}</td>
						@for ($k = 0; $k < $tot_score_asp; $k++)
						@php 
							$values = $scoreval->reportLeggerLessonData($arr_lesson[$i]['id'], $requests['class_id'], $requests['semester_id'], $student->id, $arr_score_asp[$k][2]);
							$tot_avg_student += $values[0]->value;
							$jtot_avg_student += 1;
							$avg_lesson[$k][0] += $values[0]->value;
							$avg_lesson[$k][1] += 1;
							$avg_score = $jtot_avg_student == 0 ? 0 : round($tot_avg_student/$jtot_avg_student, 2);
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
							$valavglesson = $jsum_avg_lesson == 0 ? 0 : round($sum_avg_lesson/$jsum_avg_lesson, 2);
						@endphp
						<td class="text-center"><b>{{ number_format($valavglesson,2) }}</b></td>
						<td></td>
						@endfor
						<td class="text-center"><b>{{ number_format($valsumavg,2) }}</b></td>
					</tr>
					@endfor
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function exportAcademicReportLessonAll(document) {
		var payload = {
            is_all : 1,
			class_id : {{ $requests['class_id'] }},
			semester_id : {{ $requests['semester_id'] }},
			department : "{{ $requests['department'] }}",
			schoolyear : "{{ $requests['schoolyear'] }}",
			schoolyear_id : "{{ $requests['schoolyear_id'] }}",
			class : "{{ $requests['class'] }}",
			semester : "{{ $requests['semester'] }}",
        }
        exportDocument("{{ url('academic/report/assessment/score/legger/lesson/export-') }}" + document,payload,"Ekspor data Legger Pelajaran ke "+ document.toUpperCase(),"{{ csrf_token() }}")
	}
</script>