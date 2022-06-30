@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $subtotal = $equities[1]->value + $equities[2]->value + $equities[3]->value;
  $total = $equities[0]->value + $subtotal;
@endphp
<html>
<head>
  <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN PERUBAHAN EKUITAS PEMILIK</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href="file:///{{ public_path('css/report.css') }}" rel="stylesheet" />
</head>
<body>
  <div>
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
    <br/>
  <div class="text-center" style="font-size:16px;"><b>LAPORAN PERUBAHAN EKUITAS PEMILIK</b></div>
  <br/>
  <br/>
  <table class="table no-border" style="font-size: 13px;font-weight:700">
      <tbody>
        <tr>
          <td style="width:12%;">Departemen</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:20%;">-- SEMUA --</td>
          <td style="width:13%;">Periode Tanggal</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:25%;">{{ $requests['start'] }} s/d {{ $requests['end'] }}</td>
          <td style="width:12%;">Mata Uang</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:30%;">Rupiah (Rp)</td>
        </tr>
        <tr>
          <td style="width:12%;">Tahun Buku</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:20%;">{{ $requests['bookyear'] }}</td>
          <td style="width:13%;">Dicetak oleh</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:25%;">{{ auth()->user()->name }}</td>
          <td style="width:12%;">Tanggal Cetak</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:30%;">{{ date('d/m/Y H:i:s') }}</td>
        </tr>
      </tbody>
    </table>
    <br/>
  <table class="table no-border row" style="font-size: 13px;width: 100%;">
    <thead>
      <tr>
        <th class="border-bottom text-left">Deskripsi</th>
        <th class="border-bottom text-right">Nilai</th>
      </tr>
    </thead>
    <tbody>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Ekuitas pemilik awal periode {{ $month }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($equities[0]->value,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Penambahan Ekuitas Pemilik</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span">&nbsp;&nbsp;Pendapatan Bersih pada {{ $month }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span">{{ number_format($equities[1]->value,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span">&nbsp;&nbsp;Investasi Kurun Periode {{ $month }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span">{{ number_format($equities[2]->value,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span">&nbsp;&nbsp;Penarikan pada {{ $month }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span">{{ number_format($equities[3]->value,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Total Penambahan Ekuitas Pemilik</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($subtotal,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Ekuitas pemilik per {{ $lastdate }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($total,2) }}</span>
          </td>
      </tr>
    </tbody>
  </table>
</body>
</html>