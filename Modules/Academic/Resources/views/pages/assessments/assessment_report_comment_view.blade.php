<div class="container-fluid">
	<div class="row">
        <div class="col-12 p-2">
        	<table class="table table-bordered table-sm">
        		<thead>
        			<tr>
        				<th class="text-center" width="3%">No.</th>
        				<th class="text-center">Santri</th>
        				@foreach ($exams as $exam)
        				<th class="text-center" width="18%">{{ $exam->remark }}</th>
        				@endforeach
        				<th class="text-center" width="18%">Spiritual</th>
        				<th class="text-center" width="18%">Sosial</th>
        			</tr>
        		</thead>
        		<tbody>
        			@php $i = 1; @endphp
        			@foreach ($students as $student)
    				<tr>
    					<td class="text-center">{{ $i }}</td>
    					<td>NIS: <b>{{ $student->student_no }}</b><br/>Nama: <b>{{ $student->name }}</b></td>
    					@foreach ($exams as $exam)
	    					@foreach ($finals as $final)
	    						@if ($final->student_id == $student->id && $final->score_aspect_id == $exam->score_aspect_id)
	    							@php $comment = isset($final->comment) ? $final->comment : '-'; @endphp
	    							<td style="vertical-align:top !important;">{!! html_entity_decode(str_replace('<br>', '', $comment)) !!}<br/>Nilai:&nbsp;<b>{{ number_format($final->value,2) }}</b>, Predikat:&nbsp;<b>{{ $final->value_letter }}</b></td>
	    						@endif
	    					@endforeach
    					@endforeach
    					@if (count($comments) > 0)
	    					@foreach ($comments as $comment)
	    						@if ($comment->student_id == $student->id)
	    							@php $comment_cont = isset($comment->comment) ? $comment->comment : '-<br/>'; @endphp
	    							<td style="vertical-align:top !important;">{!! html_entity_decode($comment_cont) !!}<br/><b>Predikat:</b>&nbsp;{{ $comment->type }}</td>
	    						@endif
	    					@endforeach
    					@endif
    				</tr>
        			@php $i++; @endphp
        			@endforeach
        		</tbody>
        	</table>
        </div>
    </div>
</div>