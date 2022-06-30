@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Presensi Pelajaran Santri</title>
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
      <div class="text-center" style="font-size:16px;"><b>Data Presensi Pelajaran Santri</b></div>
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
              <td style="width:10%;">Tingkat</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->grade }}</td>
            </tr>
            <tr>
              <td style="width:10%;">NIS</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->studentno }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Nama</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->student }}</td>
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
              <th class="text-center">TANGGAL</th>
              <th class="text-center">JAM</th>
              <th class="text-center">KELAS</th>
              <th class="text-center">CATATAN</th>
              <th class="text-center">PELAJARAN</th>
              <th class="text-center">GURU</th>
              <th class="text-center">MATERI</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($presences as $val)
              <tr>
                <td class="text-center">{{ $num }}</td>
                <td class="text-center" width="8%">{{ Carbon\Carbon::createFromFormat('Y-m-d',$val['date'])->format('d/m/Y') }}</td>
                <td class="text-center" width="5%">{{ $val['time'] }}</td>
                <td class="text-center">{{ $val['class'] }}</td>
                <td>{{ $val['remark'] }}</td>
                <td class="text-center">{{ $val['lesson'] }}</td>
                <td>{{ $val['employee'] }}</td>
                <td>{{ $val['subject'] }}</td>
              </tr> 
              @php $num++; @endphp
            @endforeach
          </tbody>
        </table>
        <br/>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:20%;">Jumlah Kehadiran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->sum_present }}</td>
            </tr>
            <tr>
              <td style="width:20%;">Jumlah Ketidakhadiran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->sum_absent }}</td>
            </tr>
            <tr>
              <td style="width:20%;">Jumlah Seharusnya</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->sum_required }}</td>
            </tr>
            <tr>
              <td style="width:20%;">Persentase Kehadiran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload->sum_percent }}</td>
            </tr>
          </tbody>
        </table>
      </div>
  </body>
</html>