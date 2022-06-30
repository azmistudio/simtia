@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Presensi Pelajaran Santri Tidak Hadir</title>
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
      <div class="text-center" style="font-size:16px;"><b>Data Presensi Pelajaran Santri Tidak Hadir</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->department }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->schoolyear }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->semester }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tingkat/Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->grade .' / '. ucwords($payload->class) }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Periode</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->start_date .' s.d '. $payload->end_date }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table style="width:100%;">
          <thead>
            <tr>
              <th class="text-center" width="4%">NO.</th>
              <th class="text-center">NIS</th>
              <th>NAMA</th>
              <th>KELAS</th>
              <th>PELAJARAN</th>
              <th class="text-center" width="7%">PRESENSI</th>
              <th class="text-center" width="7%">TANGGAL</th>
              <th class="text-center">KETERANGAN</th>
              <th class="text-center" width="7%">NO. HP</th>
              <th class="text-center">ORANG TUA/WALI</th>
              <th class="text-center">HP ORANG TUA</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($payload->rows as $val)
              <tr>
                <td class="text-center">{{ $num }}</td>
                <td class="text-center">{{ $val->student_no }}</td>
                <td>{{ $val->student }}</td>
                <td>{{ $val->class }}</td>
                <td class="text-center">{{ $val->lesson }}</td>
                <td class="text-center">{{ $val->presence }}</td>
                <td class="text-center">{{ $val->date }}</td>
                <td>{{ $val->remark }}</td>
                <td class="text-center">{{ $val->mobile }}</td>
                <td>{{ $val->parent }}</td>
                <td>{{ $val->parent_mobile }}</td>
              </tr> 
              @php $num++; @endphp
            @endforeach
          </tbody>
        </table>
      </div>
  </body>
</html>