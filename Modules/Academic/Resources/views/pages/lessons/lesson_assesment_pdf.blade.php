<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Aturan Perhitungan Nilai Rapor Santri</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Aturan Perhitungan Nilai Rapor Santri</span><br/>
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
            <th class="text-left">BOBOT ATURAN PENILAIAN (%)</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($groups as $val)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $val->department }}</td>
            <td class="text-left">{{ $val->lesson }}</td>
            <td class="text-left">{{ $val->employee }}</td>
            <td class="text-center">{{ $val->grade }}</td>
            <td class="text-left">{{ $val->score_aspect }}</td>
            <td>
              @foreach ($models as $model)
                @if (
                  $val->employee_id == $model->employee_id &&
                  $val->grade_id == $model->grade_id &&
                  $val->lesson_id == $model->lesson_id &&
                  $val->score_aspect_id == $model->score_aspect_id
                )
                  {{ strtoupper($model->getLessonExam->subject) }}:&nbsp;{{ number_format($model->value,1) }}<br/>
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