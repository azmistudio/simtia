@php
$schedules = array();
$mask = NULL;
for($i = 1; $i <= 7; $i++)
{
	$mask[$i] = 0;
}
foreach ($schedule as $val)
{
	$schedules[$val->day_id][$val->from_time] = $val;
}
@endphp
<div class="container-fluid">
	<div class="row">
		<div class="col-12">
			<table class="table table-bordered table-schedule">
				<thead>
					<tr>
						<th class="text-center">Jam</th>
						<th class="text-center">Senin</th>
						<th class="text-center">Selasa</th>
						<th class="text-center">Rabu</th>
						<th class="text-center">Kamis</th>
						<th class="text-center">Jum'at</th>
						<th class="text-center">Sabtu</th>
						<th class="text-center">Ahad</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($times as $key => $val)
					<tr>
						<td class="text-center row-time">{{ $val->time }}. {{ substr($val->start, 0, 5) . ' - ' . substr($val->end, 0, 5) }}</td>
						@php 
							$key += 1;
							for ($i = 1; $i <= 7; $i++)
							{
								if ($mask[$i] == 0) 
								{
									if (isset($schedules[$i][$key]))
									{
										$mask[$i] = $schedules[$i][$key]['to_time'] - 1;
										$c = "<td class='text-center' rowspan='".$schedules[$i][$key]['to_time']."'>";
										$c.= "Kelas: ".$schedules[$i][$key]['class']."<br>";
										$c.= "<b>".$schedules[$i][$key]['employee']."</b><br>";
										$c.= "<b>".strtoupper($schedules[$i][$key]['lesson'])."</b><br>";
										$c.= $schedules[$i][$key]['teaching_status']."<br>";
										$c.= "</td>";
										echo $c;
									} else {
										echo '<td></td>';
									}
								} else {
									--$mask[$i];
								}
							}
						@endphp
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>
