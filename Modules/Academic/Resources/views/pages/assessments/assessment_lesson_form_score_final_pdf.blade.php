@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Form Pengisian Nilai Akhir Santri</title>
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
      <div class="text-center" style="font-size:16px;"><b>Form Pengisian Nilai Akhir Santri</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td style="width:30%;">{{ $request['department'] }}</td>
              <td style="width:20%;">Pelajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['lesson'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['school_year'] }}</td>
              <td style="width:20%;">Dasar Penilaian</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['aspect'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['semester'] }}</td>
              <td style="width:20%;">Jenis Pengujian</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['exam'] }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $request['grade'] .' - '. $request['class'] }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table style="width:100%;">
          <thead>
            <tr>
                <th class="text-center" width="4%">NO.</th>
                <th class="text-center" width="15%">NIS</th>
                <th class="text-center" width="30%">NAMA</th>
                @php $x = 1; @endphp
                @foreach ($exams as $exam)
                  <th class="text-center" width="15%">{{ $exam->getLessonExam->code.'-'.$x }}<br/>{{ \Carbon\Carbon::createFromFormat('Y-m-d', $exam->date)->format('d/m/Y') }}</th>
                @php $x++; @endphp
                @endforeach
                <th class="text-center">Rata - Rata Santri</th>
                <th class="text-center">Nilai Akhir </th>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($rows as $row => $val)
              <tr>
                <td class="text-center">{{ $x }}</td>
                <td class="text-center">{{ $val->student_no }}</td>
                <td class="text-left">{{ ucwords($val->student) }}</td>
                @foreach ($val as $k => $v) 
                  @if (str_contains($k, $lesson_exam))
                    @php 
                      $ids = explode('_', $k);
                      $id = $ids[1];
                      $col = $lesson_exam.'_'.$id;
                      $field = $val->{$col};
                    @endphp
                    <td class="text-center">{{ $field }}</td>
                  @endif
                @endforeach
                <td class="text-center">{{ number_format($val->avg_score,2) }}</td>
                <td class="text-center"></td>
              </tr> 
              @php $x++; @endphp
            @endforeach
          </tbody>
        </table>
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