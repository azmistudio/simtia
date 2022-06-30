@inject('exams', 'Modules\Academic\Repositories\Lesson\LessonExamTypeEloquent')
@inject('scores', 'Modules\Academic\Repositories\Exam\ExamEloquent')
@php
	$PageHeight = intval(str_replace('px', '', $requests['height'])) - 135 . "px";
	$teacher_name = isset($teacher) ? $teacher['teacher'] : '-';
	$teacher_id = isset($teacher) ? $teacher['id'] : 0;
@endphp
@if (count($semesters) < 1)
	<h6>Belum ada data tersedia.</h6>
@else
<div id="tab-report-student-exam-score-avg-detail" class="easyui-tabs" plain="true" narrow="true">
	@foreach ($semesters as $semester)
	<div title="Semester {{ strtoupper($semester['semester']) }}">
		<p class="pl-3 pt-3 mb-1"><b>Pelajaran {{ $requests['lesson'] }}</b></p>
		<p class="pl-3 mb-1"><b>Guru {{ $teacher_name }}</b></p>
		<br/>
		<div style="height:{{ $PageHeight }};overflow-y: auto;">
			<div class="easyui-tabs borderless" plain="true" narrow="true">
				@foreach ($score_aspects as $aspect)
				@if ($semester['id'] == $aspect->semester_id)
				<div title="{{ $aspect->basis .' - '. $aspect->remark }}">
					<div style="">
						<div class="container-fluid">
							<div class="row">
								<div class="col-12 text-right">
		                    		<a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportExamScoreStudentAvg('{{ $semester['id'] }}','{{ strtoupper($semester['semester']) }}','{{ $aspect->basis .' - '. $aspect->remark }}',{{ $aspect->id }})">Ekspor PDF</a>
								</div>
								<div class="col-12">
									@foreach (
										$exams->reportAssessment(
											$requests['lesson_id'],
											$requests['grade_id'],
											$aspect->id,
											$teacher_id
										) as $exam
									)
									<span><b>{{ strtoupper($exam->code) .' - '. strtoupper($exam->subject) }}</b></span>
									<br/>
									<br/>
									{{-- scores --}}
									@php
										$y = 1;
										$cnt = array();
										$scorevals = $scores->reportAssessmentScores($requests['lesson_id'], $requests['student_id'], $requests['class_id'], $semester['id'], $exam->id, $exam->assessment_id);
									@endphp
									<table class="table table-sm table-bordered" style="width:100%;">
										<thead>
											<tr>
												<th class="text-center" width="5%">No.</th>
												<th class="text-center">Tanggal/Materi</th>
												<th class="text-center" width="10%">Nilai</th>
												<th class="text-center" width="15%">Rata - Rata Kelas</th>
												<th class="text-center" width="10%">%</th>
												<th class="text-center" width="15%">Rata - Rata Nilai</th>
												<th class="text-center">Nilai Akhir</th>
											</tr>
										</thead>
										<tbody>
											@foreach ($scorevals as $score)
											<tr>
												<td class="text-center">{{ $y }}</td>
												<td>{{ $score['date'] }}<br/>{{ $score['description'] }}</td>
												<td class="text-center">{{ $score['score'] }}</td>
												<td class="text-center">{{ $score['avg_class'] }}</td>
												<td class="text-center">{{ $score['percent'] }}</td>
												@if ($y == 1)
												<td class="text-center" rowspan="{{ count($scorevals) }}">{{ $score['avg_score'] }}</td>
												<td class="text-center" rowspan="{{ count($scorevals) }}">{{ $score['final_score'] }}</td>
												@endif
											</tr>
											@php $y++; @endphp
											@endforeach
										</tbody>
									</table>
									@endforeach
								</div>
							</div>
						</div>
					</div>
				</div>
				@endif
				@endforeach
			</div>
		</div>
	</div>
	@endforeach
</div>
@endif
<script type="text/javascript">
	function exportAcademicReportExamScoreStudentAvg(semester_id, semester, score_aspect, score_aspect_id) {
        var payload = {
            department: "{{ $requests['department'] }}",
            schoolyear: "{{ $requests['schoolyear'] }}",
            grade: "{{ $requests['grade'] }}",
            class: "{{ $requests['class'] }}",
            lesson: "{{ $requests['lesson'] }}",
            student_no: "{{ $requests['student_no'] }}",
            student: "{{ $requests['student'] }}",
            semester_id: semester_id,
            semester: semester,
            score_aspect: score_aspect,
            department_id: {{ $requests['department_id'] }},
            grade_id: {{ $requests['grade_id'] }},
            student_id: {{ $requests['student_id'] }},
            lesson_id: {{ $requests['lesson_id'] }},
            class_id: {{ $requests['class_id'] }},
            employee_id: {{ $teacher_id }},
            score_aspect_id: score_aspect_id,
        }
        exportDocument("{{ url('academic/report/assessment/score/average/export-pdf') }}",payload,"Ekspor Rata-Rata Nilai Santri ke PDF","{{ csrf_token() }}")
    }
</script>