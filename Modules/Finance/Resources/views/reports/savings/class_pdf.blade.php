@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN TRANSAKSI TABUNGAN KELAS</title>
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
      <br/>
      <br/>
      <div class="text-center" style="font-size:16px;"><b>LAPORAN TRANSAKSI TABUNGAN KELAS</b></div>
      <br/>
      <br/>
      <table class="table no-border" width="100%">
        <tbody>
          <tr>
            <td style="width:3%;">Departemen</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td style="width:30%;"><b>{{ $requests->department }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Tahun Ajaran</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->schoolyear }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Tingkat</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->grade }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Jenis Tabungan</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->saving }}</b></td>
          </tr>
        </tbody>
      </table>
      <br/>
      <br/>
      <table width="100%">
        <thead>
          <tr>
            <th class="text-center">No.</th>
            <th class="text-center">NIS</th>
            <th>Nama</th>
            <th class="text-center">Kelas</th>
            <th class="text-center">Saldo Tabungan</th>
            <th class="text-center">Total Setoran</th>
            <th class="text-center">Setoran Terakhir</th>
            <th class="text-center">Total Tarikan</th>
            <th class="text-center">Tarikan Terakhir</th>
          </tr>
        </thead>
        <tbody>
        @php $x = 1; @endphp
        @foreach ($requests->rows as $data)
          <tr>
            <td class="text-center" width="3%">{{ $x++ }}</td>
            <td class="text-center" width="7%">{{ $data->student_no }}</td>
            <td width="">{{ $data->name }}</td>
            <td class="text-center" width="10%">{{ $data->class }}</td>
            <td class="text-right" width="10%">{{ $data->balance }}</td>
            <td class="text-right" width="10%">{{ $data->total_saving }}</td>
            <td class="text-right" width="10%">{!! $data->last_saving !!}</td>
            <td class="text-right" width="10%">{{ $data->total_withdraw }}</td>
            <td class="text-right" width="10%">{!! $data->last_withdraw !!}</td>
          </tr>
        @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-right"><b>TOTAL</b></th>
            <th class="text-right"><b>{!! $requests->footers[0]->balance !!}</b></th>
            <th class="text-right"><b>{!! $requests->footers[0]->total_saving !!}</b></th>
            <th></th>
            <th class="text-right"><b>{!! $requests->footers[0]->total_withdraw !!}</b></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
  </body>
</html>