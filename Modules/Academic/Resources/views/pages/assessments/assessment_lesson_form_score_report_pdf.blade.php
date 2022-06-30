@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Form Pengisian Nilai Rapor Santri</title>
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
      <div class="text-center" style="font-size:16px;"><b>Form Pengisian Nilai Rapor Santri</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $request['department'] }}</td>
              <td style="width:20%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['grade'] .' - '. $request['class'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['school_year'] }}</td>
              <td style="width:20%;">Pelajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['lesson'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['semester'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        @foreach ($assessments as $assessment)
        <h3>Aspek Penilaian: {{ strtoupper($assessment->getScoreAspect->remark) }}</h3>
        @php $i = 0; $cols = array(); @endphp
        @foreach ($assessments_det as $det)
          @if ($det->score_aspect_id == $assessment->score_aspect_id)
            @php $cols[$i++] = array($det->exam); @endphp
          @endif
        @endforeach
        <table style="width:100%;">
          <thead>
            <tr>
              <th rowspan="2" class="text-center" width="4%">NO.</th>
              <th rowspan="2" class="text-center" width="15%">NIS</th>
              <th rowspan="2" class="text-center" width="30%">NAMA</th>
              <th colspan="{{ count($cols) }}" class="text-center">NILAI AKHIR</th>
              <th colspan="2" class="text-center">NILAI {{ strtoupper($assessment->getScoreAspect->remark) }}</th>
            </tr>
            <tr>
              @php $i = 0; $exams = array(); @endphp
              @foreach ($assessments_det as $det)
                @if ($det->score_aspect_id == $assessment->score_aspect_id)
                @php $exams[$i++] = array($det->exam); @endphp
                  <th class="text-center">{{ $det->getLessonExam->code }} ({{ $det->value }})</th>
                @endif
              @endforeach
              <th class="text-center">Angka</th>
              <th class="text-center">Huruf</th>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($students as $student)
              <tr>
                <td class="text-center">{{ $x }}</td>
                <td class="text-center">{{ $student->student_no }}</td>
                <td class="text-left">{{ ucwords($student->name) }}</td>
                @foreach ($exams as $exam)
                  <td class="text-center"></td>  
                @endforeach   
                <td class="text-center"></td>  
                <td class="text-center"></td>   
              </tr> 
            @php $x++; @endphp
            @endforeach
          </tbody>
        </table>
        @endforeach
        <br/>
        <br/>
        <div style="float:right;">
          <table class="table no-border">
            <tbody>
              <tr>
                <td class="text-center">Guru</td>
              </tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr>
                <td class="text-center">{{ $request['teacher'] }}<br/>_______________________<br/>NIP. {{ $teachers->employee_id }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </body>
</html>