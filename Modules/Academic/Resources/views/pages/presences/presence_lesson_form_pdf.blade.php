@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Form Presensi Pelajaran</title>
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
      <div class="text-center" style="font-size:16px;"><b>Form Presensi Pelajaran</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getGrade->getDepartment->name }}</td>
              <td style="width:10%;">Tanggal</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getSchoolYear->school_year }}</td>
              <td style="width:10%;">Nama Guru</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________</td>
            </tr>
            <tr>
              <td style="width:10%;">Semester</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getGrade->getSemesterByDept->semester }}</td>
              <td style="width:10%;">Sebagai</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________</td>
            </tr>
            <tr>
              <td style="width:10%;">Kelas</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $header->getGrade->grade .' - '. $header->class }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Pelajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $lesson }}</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:20%;">Materi</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;"></td>
              <td style="width: 1%;text-align:center;"></td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;">Objektif</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;"></td>
              <td style="width: 1%;text-align:center;"></td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;">Refleksi</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;"></td>
              <td style="width: 1%;text-align:center;"></td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;">Materi Selanjutnya</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;"></td>
              <td style="width: 1%;text-align:center;"></td>
              <td>_______________________________________________________________________________________________________________________________________________</td>
            </tr>
            <tr>
              <td style="width:20%;">Keterlambatan</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>_______________ menit</td>
            </tr>
          </tbody>
        </table>
        <br/>
        <table style="width:100%;">
          <thead>
            <tr>
                <th class="text-center" width="4%">NO.</th>
                <th class="text-center" width="10%">NIS</th>
                <th class="text-center" width="30%">NAMA</th>
                <th class="text-center" width="10%">PRESENSI</th>
                <th class="text-center">CATATAN</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($body as $val)
              <tr>
                <td class="text-center">{{ $num }}</td>
                <td class="text-center">{{ $val->student_no }}</td>
                <td class="text-left">{{ ucwords($val->name) }}</td>
                <td class="text-center"></td>
                <td class="text-center"></td>
              </tr> 
              @php $num++; @endphp
            @endforeach
          </tbody>
        </table>
        <br/>
        <br/>
        <div style="float:left;clear: right;">
          <table class="table no-border">
            <tbody>
              <tr>
                <td class="text-center">Ketua Kelas</td>
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
                <td class="text-center">( _______________________ )</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
  </body>
</html>