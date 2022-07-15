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
	$tot_avg_student = 0;
	$jtot_avg_student = 0;
	$sum_avg_student = 0;
	$jsum_avg_student = 0;
	$avg_score = 0;
	$no = 1;
@endphp
<div class="container-fluid">
	<div class="row">
		<div class="col-12 text-right p-1">
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportLesson('excel',{{ count($students) }})">Ekspor Excel</a>
		</div>
		<div class="col-12 p-1">
			<table class="table table-bordered table-sm" width="100%">
				<thead>
					<tr>
						<th class="text-center" rowspan="2">No.</th>
						<th class="text-center" rowspan="2">NIS</th>
						<th class="text-center" rowspan="2">Nama</th>
						@for ($i = 0; $i < $count_aspect; $i++)
						<th class="text-center" colspan="2">{{ $score_asp_arr[$i][1] }}</th>
						@endfor
						<th class="text-center" rowspan="2">Rata - Rata Santri</th>
					</tr>
					<tr>
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
							$values = $scoreval->reportLeggerLessonData($requests['lesson_id'], $requests['class_id'], $requests['semester_id'], $student->id, $score_asp_arr[$k][2]);
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
					@endforeach
					@php $valsumavg = $jsum_avg_student == 0 ? 0 : round($sum_avg_student / $jsum_avg_student, 2); @endphp
					<tr>
						<td class="text-right" colspan="3"><b>RATA - RATA</b></td>
						@for ($l = 0; $l < $count_aspect; $l++)
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
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function exportAcademicReportLesson(document, total) {
		if (total > 0) {
			var payload = {
	            is_all : 0,
	            lesson_id: {{ $requests['lesson_id'] }},
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
	}
</script>