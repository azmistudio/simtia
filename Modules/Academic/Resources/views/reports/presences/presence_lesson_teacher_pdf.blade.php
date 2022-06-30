@php
  $t_status = array();
  $t_times = array();
  $sub_total = array();
  $sub_times = array();
  $total = 0;
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Presensi Pelajaran Pengajar</title>
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
      <div class="text-center" style="font-size:16px;"><b>Data Presensi Pelajaran Pengajar</b></div>
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
              <th class="text-center">PELAJARAN</th>
              <th class="text-center">STATUS</th>
              <th class="text-center">TELAT</th>
              <th class="text-center">JAM</th>
              <th class="text-center">MATERI</th>
              <th class="text-center">KETERANGAN</th>
            </tr>
          </thead>
          <tbody>
            @php $num = 1; @endphp
            @foreach ($payload->rows as $val)
              <tr>
                <td class="text-center">{{ $num }}</td>
                <td class="text-center" width="7%">{{ $val->date }}</td>
                <td class="text-center" width="6%">{{ $val->time }}</td>
                <td class="text-center" width="7%">{{ $val->class }}</td>
                <td class="text-center" width="7%">{{ $val->lesson }}</td>
                <td class="text-center" width="7%">{{ $val->status }}</td>
                <td class="text-center" width="4%">{{ $val->late }}</td>
                <td class="text-center" width="6%">{{ $val->times }}</td>
                <td>{{ $val->subject }}</td>
                <td>{{ $val->remark }}</td>
              </tr> 
              @php $t_status[] = strtoupper($val->status); $t_times[] = array('status' => strtoupper($val->status), 'times' => $val->minutes) ; $num++; @endphp
            @endforeach
          </tbody>
        </table>
        <br/>
        <table style="width:30%">
          <thead>
            <tr>
              <th class="text-center">Status</th>
              <th class="text-center">Pertemuan</th>
              <th class="text-center">Jumlah Jam</th>
            </tr>
          </thead>
          <tbody>
            @php $sub_total = array_count_values($t_status); @endphp
            @foreach ($status as $key => $val)
              <tr>
                <td><b>{{ $val }}</b></td>
                <td class="text-center">
                  @php $sum = isset($sub_total[strtoupper($val)]) ? $sub_total[strtoupper($val)] : 0; @endphp
                  {{ $sum }}
                </td>
                <td class="text-center">
                  @foreach ($t_times as $time)
                    @if (strtoupper($val) == $time['status'])
                      @php $total += $time['times']; @endphp
                    @endif
                  @endforeach
                  {{ $total / 60 }}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </body>
</html>