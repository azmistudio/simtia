@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN TRANSAKSI PENGELUARAN</title>
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
      <div class="text-center" style="font-size:16px;"><b>LAPORAN TRANSAKSI PENGELUARAN</b></div>
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
            <td style="width:3%;">Tahun Buku</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->bookyear }}</b></td>
          </tr>
          <tr>
            <td style="width:3%;">Periode</td>
            <td style="width: 1%;text-align:center;">:</td>
            <td><b>{{ $requests->start }}</b> s.d <b>{{ $requests->end }}</b></td>
          </tr>
        </tbody>
      </table>
      <br/>
      <br/>
      <table width="100%">
        <thead>
          <tr>
            <th class="text-center">No.</th>
            <th class="text-center">Tanggal</th>
            <th>Pemohon</th>
            <th>Penerima</th>
            <th class="text-center">Jumlah</th>
            <th>Keperluan</th>
            <th>Petugas</th>
          </tr>
        </thead>
        <tbody>
        @php $x = 1; @endphp
        @foreach ($requests->rows as $data)
          <tr>
            <td class="text-center">{{ $x++ }}</td>
            <td class="text-center" width="7%">{{ $data->trans_date }}</td>
            <td width="">{{ $data->requested_name }}</td>
            <td width="">{{ $data->received_name }}</td>
            <td class="text-right">Rp{{ number_format($data->total,2) }}</td>
            <td width="">{!! $data->purpose !!}</td>
            <td width="">{{ $data->employee }}</td>
          </tr>
        @endforeach
        </tbody>
        <tfoot>
          <tr>
            <th colspan="4" class="text-center"><b>TOTAL</b></th>
            <th class="text-right"><b>{!! $requests->footers[0]->total_val !!}</b></th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
  </body>
</html>