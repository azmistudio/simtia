@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Form Presensi Harian</title>
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
      <div class="text-center" style="font-size:16px;"><b>Form Presensi Harian</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getGrade->getDepartment->name }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getSchoolYear->school_year }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getGrade->getSemesterByDept->semester }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getGrade->grade .' - '. $header->class }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tanggal</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>____________________ s.d ____________________</td>
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
            @php $x = 1; @endphp
            @foreach ($body as $val)
              <tr>
                <td class="text-center">{{ $x }}</td>
                <td class="text-center">{{ $val->student_no }}</td>
                <td class="text-left">{{ ucwords($val->name) }}</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
                <td class="text-center"></td>
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
                <td class="text-center">Wali Kelas</td>
              </tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr><td></td></tr>
              <tr>
                <td class="text-center">( _______________________ )</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </body>
</html>