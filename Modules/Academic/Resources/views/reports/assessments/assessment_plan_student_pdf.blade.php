@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Statistik Rata-Rata RPP setiap Santri</title>
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
      <div class="text-center" style="font-size:16px;"><b>Statistik Rata-rata RPP setiap Santri</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->department }}</td>
              <td style="width:10%;">Pelajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->lesson }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->schoolyear }}</td>
              <td style="width:10%;">Ujian</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->lesson_exam }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tingkat</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->grade }}</td>
              <td style="width:10%;">RPP</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->lesson_plan }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->semester }}</td>
              <td style="width:10%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->class }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <div style="width:100%;height: 340px;text-align: center;">
          {!! $data[1] !!}  
        </div>
        <table style="width:100%;">
          <thead>
            <tr>
              <th class="text-center" width="4%">NO.</th>
              <th class="text-center">NIS</th>
              <th class="text-left">NAMA</th>
              <th class="text-center">RATA - RATA</th>
            </tr>
          </thead>
          <tbody>
            @php $x = 1; @endphp
            @foreach ($data[0] as $val)
              <tr>
                <td class="text-center">{{ $x }}</td>
                <td class="text-center">{{ $val['student_no'] }}</td>
                <td class="text-left">{{ $val['student'] }}</td>
                <td class="text-center">{{ $val['total'] }}</td>
              </tr> 
              @php $x++; @endphp
            @endforeach
          </tbody>
        </table>
      </div>
  </body>
</html>