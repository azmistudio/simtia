@inject('scoreval', 'Modules\Academic\Repositories\Exam\ExamReportEloquent')
@php
	$tot_lesson = count($lessons);
    $tot_score_asp = count($score_aspects);
    $arr_tot_score_asp_class = array();
    $arr_tot_score_asp = array();
@endphp
<div class="container-fluid">
	<div class="row">
		<div class="col-12 text-right p-1">
            <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--ExcelDocument" plain="true" onclick="exportAcademicReportLeggerClass('excel')">Ekspor Excel</a>
		</div>
		<div class="col-12 p-1">
			<table class="table table-bordered table-sm" width="100%">
				<thead>
					<tr>
						<th class="text-center" rowspan="2">No.</th>
						<th class="text-center" rowspan="2">NIS</th>
						<th class="text-center" rowspan="2">Nama</th>
						@for ($i=0; $i < $tot_lesson; $i++) 
						@php
					    	$id_lesson = $arr_lessons[$i][0];
							$lesson = $arr_lessons[$i][1];
					    	$tot_aspect = count($arr_asp_lessons[$id_lesson]);
					    @endphp
						<th class="text-center" colspan="{{ $tot_aspect }}">{{ $lesson }}</th>
						@endfor
						<th class="text-center" colspan="{{ $tot_score_asp }}">Rata - Rata Santri</th>
					</tr>
					<tr>
						@for ($i = 0; $i < $tot_lesson; $i++)
						@php
							$id_lesson = $arr_lessons[$i][0];
							$tot_aspect = count($arr_asp_lessons[$id_lesson]);
						@endphp
						@for ($j = 0; $j < $tot_aspect; $j++)
						@php
							$id_aspect = $arr_asp_lessons[$id_lesson][$j];
						@endphp
						<th class="text-center">{{ $aspects[$id_aspect] }}</th>
						@endfor
						@endfor
						@foreach ($score_aspects as $aspect)
						<th class="text-center">{{ $aspect->basis }}</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					@php $no = 1; @endphp
					@foreach ($students as $student)
					@php $col_cnt = -1; @endphp
					<tr>
						<td class="text-center">{{ $no }}</td>
						<td class="text-center">{{ $student->student_no }}</td>
						<td>{{ $student->student }}</td>
						@for ($i = 0; $i < $tot_lesson; $i++)
							@php
								$id_lesson = $arr_lessons[$i][0];
								$tot_aspect = count($arr_asp_lessons[$id_lesson]);
							@endphp
							@for ($j = 0; $j < $tot_aspect; $j++)
								@php
									$col_cnt += 1;
									$id_aspect = $arr_asp_lessons[$id_lesson][$j];
									$value = $scoreval->reportLeggerClassScore($student->id, $id_lesson, $requests['semester_id'], $requests['class_id'], $id_aspect);
									// score lesson
									if (!empty($value))
									{
										if (array_key_exists($id_aspect, $arr_tot_score_asp))
										{
											$arr_tot_score_asp[$id_aspect][0] += $value->value;
											$arr_tot_score_asp[$id_aspect][1] += 1;
										} else {
											$arr_tot_score_asp[$id_aspect] = array($value->value, 1);
										}
										if ($no == 1)
										{
											$arr_tot_score_asp_class[$col_cnt] = array($value->value, 1);
										} else {
											$arr_tot_score_asp_class[$col_cnt][0] += $value->value;
											$arr_tot_score_asp_class[$col_cnt][1] += 1;
										}
										echo '<td class="text-center">'.number_format($value->value,2).'</td>';
									} else {
										echo '<td class="text-center">-</td>';
									}
								@endphp
							@endfor
						@endfor
						{{-- avg --}}
						@php
							foreach ($score_aspects as $aspect)
							{
								$col_cnt += 1;
								$score = '-';
								$jscore = 0;
								if (array_key_exists($aspect->id, $arr_tot_score_asp))
								{
									$score = $arr_tot_score_asp[$aspect->id][0];
									$jscore = $arr_tot_score_asp[$aspect->id][1];
								}
								if ($jscore > 0)
								{
									$score = round($score/$jscore, 2);

									if ($no == 1)
									{
										$arr_tot_score_asp_class[$col_cnt] = array($score, 1);
									} else {
										$arr_tot_score_asp_class[$col_cnt][0] += $score;
										$arr_tot_score_asp_class[$col_cnt][1] += 1;
									}
								}
								echo '<td class="text-center">'.number_format($score,2).'</td>';
							}
						@endphp
					</tr>
					@php $no++; @endphp
					@endforeach
				</tbody>
				<tfoot>
					@php $jcol = count($arr_tot_score_asp_class); @endphp
					<tr>
						<th class="text-right" colspan="3">Rata - Rata Kelas</th>
						@for ($i = 0; $i < $jcol; $i++)
						@php 
							$avg = '';
							if ($arr_tot_score_asp_class[$i][1] > 0)
							{
								$avg = round($arr_tot_score_asp_class[$i][0] / $arr_tot_score_asp_class[$i][1], 2);
							}
						@endphp
						<th class="text-center">{{ number_format($avg,2) }}</th>
						@endfor
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	function exportAcademicReportLeggerClass(document) {
		var payload = {
            lesson_id: "{{ $requests['lesson_id'] }}",
			class_id : {{ $requests['class_id'] }},
			semester_id : {{ $requests['semester_id'] }},
			schoolyear_id : {{ $requests['schoolyear_id'] }},
			department : "{{ $requests['department'] }}",
			schoolyear : "{{ $requests['schoolyear'] }}",
			class : "{{ $requests['class'] }}",
			semester : "{{ $requests['semester'] }}",
			lesson : "{{ $requests['lesson'] }}",
        }
        exportDocument("{{ url('academic/report/assessment/score/legger/class/export-') }}" + document,payload,"Ekspor data Legger Kelas ke "+ document.toUpperCase(),"{{ csrf_token() }}")
	}
</script>