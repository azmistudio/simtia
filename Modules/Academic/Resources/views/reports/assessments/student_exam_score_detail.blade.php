@php
	$PageHeight = intval(str_replace('px', '', $requests['height'])) - 65 . "px";
@endphp
<div id="tab-report-student-exam-score-detail" class="easyui-tabs" plain="true" narrow="true">
	@foreach ($semesters as $semester)
	<div title="Semester {{ strtoupper($semester->semester) }}">
		<div style="height:{{ $PageHeight }};overflow-y: auto;">
			<div class="container-fluid">
				<div class="row">
					<div class="col-8"><p style="padding-top:6px;"><i class="ms-Icon ms-Icon--FlickLeft"></i><b>Pelajaran {{ $requests['lesson'] }}</b></p></div>
					<div class="col-4 text-right">
	                    <a href="javascript:void(0)" class="easyui-linkbutton lbtn" iconCls="ms-Icon ms-Icon--PDF" plain="true" onclick="exportAcademicReportExamScoreStudent('{{ $semester->id }}','{{ strtoupper($semester->semester) }}')">Ekspor PDF</a>
					</div>
					@foreach ($exams as $exam)
					<div class="col-12">
						<span><b>{{ strtoupper($exam->code) .' - '. strtoupper($exam->subject) }}</b></span>
						<br/>
						<br/>
						<table class="table table-sm table-bordered" style="width:100%;">
							<thead>
								<tr>
									<th class="text-center" width="5%">No.</th>
									<th class="text-center" width="15%">Tanggal</th>
									<th class="text-center" width="15%">Nilai</th>
									<th class="text-center">Keterangan</th>
								</tr>
							</thead>
							<tbody>
								@php $x = 1; @endphp
								@foreach ($scores as $score)
								@if ($semester->id == $score->semester_id)
								@if ($exam->id == $score->lesson_exam_id)
								<tr>
									<td class="text-center">{{ $x }}</td>
									<td class="text-center">{{ $score->date }}</td>
									<td class="text-center">{{ $score->score }}</td>
									<td>{{ $score->remark }}</td>
								</tr>
								@php $x++; @endphp
								@endif
								@endif
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th colspan="2" class="text-right">Nilai Rata - Rata</th>
									<th class="text-center">
										@foreach ($scores_avg as $avg)
										@if ($semester->id == $avg->semester_id && $exam->id == $avg->lesson_exam_id)
										{{ number_format($avg->avg,2) }}
										@endif
										@endforeach
									</th>
									<th></th>
								</tr>
							</tfoot>
						</table>
						<br/>
					</div>
					@endforeach
				</div>
			</div>
		</div>
	</div>
	@endforeach
</div>
<script type="text/javascript">
	function exportAcademicReportExamScoreStudent(semester_id, semester) {
        var payload = {
            department: "{{ $requests['department'] }}",
            schoolyear: "{{ $requests['schoolyear'] }}",
            grade: "{{ $requests['grade'] }}",
            class: "{{ $requests['class'] }}",
            lesson: "{{ $requests['lesson'] }}",
            student_no: "{{ $requests['student_no'] }}",
            student: "{{ $requests['student'] }}",
            student_id: {{ $requests['student_id'] }},
            lesson_id: {{ $requests['lesson_id'] }},
            class_id: {{ $requests['class_id'] }},
            semester_id: semester_id,
            semester: semester,
        }
        exportDocument("{{ url('academic/report/assessment/score/export-pdf') }}",payload,"Ekspor Nilai Ujian Santri ke PDF","{{ csrf_token() }}")
    }
</script>