@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Presensi Harian</title>
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
      <div class="text-center" style="font-size:16px;"><b>Data Presensi Harian</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $presences->getSemester->getDepartment->name }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $presences->getClass->getSchoolYear->school_year }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $presences->getSemester->semester }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $presences->getClass->getGrade->grade .' - '. ucwords($presences->getClass->class) }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Periode</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $presences->start_date->format('d/m/Y') .' s.d '. $presences->end_date->format('d/m/Y') }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table style="width:100%;">
          <thead>
            <tr>
                <th class="text-center" width="4%">NO.</th>
                <th class="text-center">NIS</th>
                <th class="text-center" width="30%">NAMA</th>
                <th class="text-center" width="7%">HADIR</th>
                <th class="text-center" width="7%">IJIN</th>
                <th class="text-center" width="7%">SAKIT</th>
                <th class="text-center" width="7%">ALPA</th>
                <th class="text-center" width="7%">CUTI</th>
                <th class="text-center">KETERANGAN</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($students as $val)
              <tr>
                <td class="text-center">{{ $num }}</td>
                <td class="text-center">{{ $val->getStudent->student_no }}</td>
                <td class="text-left">{{ ucwords($val->getStudent->name) }}</td>
                <td class="text-center">{{ $val->present }}</td>
                <td class="text-center">{{ $val->permit }}</td>
                <td class="text-center">{{ $val->sick }}</td>
                <td class="text-center">{{ $val->absent }}</td>
                <td class="text-center">{{ $val->leave }}</td>
                <td class="text-center">{{ $val->remark }}</td>
              </tr> 
              @php $num++; @endphp
            @endforeach
          </tbody>
        </table>
      </div>
  </body>
</html>