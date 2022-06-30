@inject('scores', 'Modules\Academic\Repositories\Exam\ExamEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Laporan Rata - Rata Ujian Santri</title>
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
      <div class="text-center" style="font-size:16px;"><b>Laporan Rata - Rata Ujian Santri</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->department }}</td>
              <td style="width:15%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->semester }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->schoolyear }}</td>
              <td style="width:15%;">Pelajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->lesson }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tingkat</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->grade }}</td>
              <td style="width:15%;">Dasar Penilaian</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->score_aspect }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->class }}</td>
              <td style="width:15%;">Santri</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->student_no .' - '. $payload->student }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        @foreach ($exams as $exam)
        <span style="font-size:14px;"><b>{{ strtoupper($exam->code) .' - '. strtoupper($exam->subject) }}</b></span>
        <br/>
        <br/>
        @php
          $y = 1; $cnt = array(); 
          $scorevals = $scores->reportAssessmentScores($payload->lesson_id, $payload->student_id, $payload->class_id, $payload->semester_id, $exam->id, $exam->assessment_id);
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
        <br/>
        <br/>
        @endforeach
      </div>
  </body>
</html>