@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Nilai Ujian Santri</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <table class="table no-border" style="width:100%;">
        <tbody>
          <tr>
            <th rowspan="2" width="100px"><img src="file:///{{ $logo }}" height="80px" /></th>
            <td><b>{{ strtoupper($profile['name']) }}</b></td>
          </tr>
            <tr>
              <td style="font-size:11px;">
                {{ $profile['address'] }}<br/>
                Telpon: {{ $profile['phone'] }} - Faksimili: {{ $profile['fax'] }}<br/>
                Website: {{ $profile['web'] }} - Email: {{ $profile['email'] }}
              </td>
            </tr>
        </tbody>
      </table>
    </div>
    <hr/>
    <div id="body">
      <br/>
      <div class="text-center" style="font-size:16px;"><b>Nilai Ujian Santri</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->department }}</td>
              <td style="width:10%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->semester }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->schoolyear }}</td>
              <td style="width:10%;">Pelajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->lesson }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tingkat</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->grade }}</td>
              <td style="width:10%;">NIS</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->student_no }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->class }}</td>
              <td style="width:10%;">Nama</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->student }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        @foreach ($exams as $exam)
        <span style="font-size:14px;"><b>{{ strtoupper($exam->code) .' - '. strtoupper($exam->subject) }}</b></span>
        <br/>
        <br/>
        <table style="width:100%;">
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
            @if ($exam->id == $score->lesson_exam_id)
            <tr>
              <td class="text-center">{{ $x }}</td>
              <td class="text-center">{{ $score->date }}</td>
              <td class="text-center">{{ $score->score }}</td>
              <td>{{ $score->remark }}</td>
            </tr>
            @php $x++; @endphp
            @endif
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <th colspan="2" class="text-right">Nilai Rata - Rata</th>
              <th class="text-center">
                @foreach ($scores_avg as $avg)
                @if ($exam->id == $avg->lesson_exam_id)
                {{ $avg->avg }}
                @endif
                @endforeach
              </th>
              <th></th>
            </tr>
          </tfoot>
        </table>
        <br/>
        <br/>
        @endforeach
      </div>
  </body>
</html>