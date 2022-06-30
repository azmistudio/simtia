<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Tabel Jadwal Guru</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Tabel Jadwal Guru</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <hr/> 
        <br/> 
        @php $payload = explode('-', $infos) @endphp
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:10%;">Guru</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload[0] }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Departemen</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload[1] }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Tahun Ajaran</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload[2] }}</td>
            </tr>
            <tr>
              <td style="width:10%;">Info Jadwal</td>
              <td style="width: 1%;text-align:center;">:</td>
              <td>{{ $payload[3] }}</td>
            </tr>
          </tbody>
        </table>
        <br/> 
      </div>
    </div>
    @php
      $schedules = array();
      $mask = NULL;
      for($i = 1; $i <= 7; $i++)
      {
        $mask[$i] = 0;
      }
      foreach ($schedule as $val)
      {
        $schedules[$val->day_id][$val->from_time] = $val;
      }
    @endphp
    <div class="container-fluid">
      <div class="row">
        <div class="col-12">
          <table class="table table-bordered table-schedule" width="100%">
            <thead>
              <tr>
                <th class="text-center" width="8%">Jam</th>
                <th class="text-center" width="13%">Senin</th>
                <th class="text-center" width="13%">Selasa</th>
                <th class="text-center" width="13%">Rabu</th>
                <th class="text-center" width="13%">Kamis</th>
                <th class="text-center" width="13%">Jum'at</th>
                <th class="text-center" width="13%">Sabtu</th>
                <th class="text-center" width="13%">Ahad</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($times as $key => $val)
              <tr>
                <td class="text-center row-time">{{ $val->time }}. {{ substr($val->start, 0, 5) . ' - ' . substr($val->end, 0, 5) }}</td>
                @php 
                  $key += 1;
                  for ($i = 1; $i <= 7; $i++)
                  {
                    if ($mask[$i] == 0) 
                    {
                      if (isset($schedules[$i][$key]))
                      {
                        $mask[$i] = $schedules[$i][$key]['to_time'] - 1;
                        $c = "<td class='text-center' rowspan='".$schedules[$i][$key]['to_time']."'>";
                        $c.= "Kelas: ".$schedules[$i][$key]['class']."<br>";
                        $c.= "<b>".strtoupper($schedules[$i][$key]['lesson'])."</b><br>";
                        $c.= $schedules[$i][$key]['teaching_status']."<br>";
                        $c.= "</td>";
                        echo $c;
                      } else {
                        echo '<td></td>';
                      }
                    } else {
                      --$mask[$i];
                    }
                  }
                @endphp
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </body>
</html>