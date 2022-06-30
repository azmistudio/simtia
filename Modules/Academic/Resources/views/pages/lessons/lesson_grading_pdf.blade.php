<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Aturan Grading Rapor</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Aturan Grading Rapor</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center" width="5%">NO.</th>
            <th class="text-center">DEPARTEMEN</th>
            <th class="text-left">PELAJARAN</th>
            <th class="text-left">GURU</th>
            <th class="text-center">TINGKAT</th>
            <th class="text-left">ASPEK PENILAIAN</th>
            <th class="text-left">ATURAN GRADING</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($groups as $group)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $group->department }}</td>
            <td class="text-left">{{ $group->lesson }}</td>
            <td class="text-left">{{ $group->employee }}</td>
            <td class="text-center">{{ $group->grade }}</td>
            <td class="text-left">{{ $group->score_aspect }}</td>
            <td>
              @foreach ($models as $model)
                @if (
                  $group->employee_id == $model->employee_id &&
                  $group->grade_id == $model->grade_id &&
                  $group->lesson_id == $model->lesson_id &&
                  $group->score_aspect_id == $model->score_aspect_id
                )
                  {{ strtoupper($model->grade) }}:&nbsp;{{ number_format($model->min,1) }}&nbsp;s.d&nbsp;{{ number_format($model->max,1) }}<br/>
                @endif
              @endforeach      
            </td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>