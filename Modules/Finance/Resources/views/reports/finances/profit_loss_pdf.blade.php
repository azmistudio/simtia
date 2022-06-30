@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $debits = 0;
  $credits = 0;
@endphp
<html>
<head>
  <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN LABA/RUGI</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <link href="file:///{{ public_path('css/report.css') }}" rel="stylesheet" />
  <style type="text/css">
    table { border-collapse: collapse; border: 1px solid #000; font-size:13px; page-break-inside: auto; }
  </style>
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
  <div class="text-center" style="font-size:16px;"><b>LAPORAN LABA/RUGI</b></div>
  <br/>
  <br/>
  <table class="table no-border" style="font-size: 13px;font-weight:700">
      <tbody>
        <tr>
          <td style="width:12%;">Departemen</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:20%;">-- SEMUA --</td>
          <td style="width:12%;">Periode Tanggal</td>
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
          <td style="width:12%;">Dicetak oleh</td>
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
        <th class="border-bottom text-right">{{ $startdate }} - {{ $lastdate }}</th>
      </tr>
    </thead>
    <tbody>
      <tr valign="top" style="height:10px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
        <td colspan="2">
            <span style="font-weight: bold;">PENDAPATAN</span>
        </td>
      </tr>
      @foreach ($profits as $profit)
      @php $debits += $profit->debit; @endphp
      @if (!$is_total)
      @if ($is_zero)
      <tr valign="top">
          <td>
              <span style="@if ($profit->parent < 1) font-weight: bold; @endif">
                  @if ($profit->parent > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;{{ $profit->name }}
                  @else
                      &nbsp; &nbsp;{{ $profit->name }}
                  @endif
              </span>
          </td>
          <td style="text-align: right;">
              <span style="@if ($profit->parent < 1) font-weight: bold; @endif">{{ number_format($profit->debit,2) }}</span>
          </td>
      </tr>
      @else
      @if ($profit->debit > 0)
      <tr valign="top">
          <td>
              <span style="@if ($profit->parent < 1) font-weight: bold; @endif">
                  @if ($profit->parent > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;{{ $profit->name }}
                  @else
                      &nbsp; &nbsp;{{ $profit->name }}
                  @endif
              </span>
          </td>
          <td style="text-align: right;">
              <span style="@if ($profit->parent < 1) font-weight: bold; @endif">{{ number_format($profit->debit,2) }}</span>
          </td>
      </tr>
      @endif
      @endif
      @endif
      @endforeach
      <tr valign="top" style="height:14px">
        <td>
            <span style="font-weight: bold;">Jumlah Pendapatan</span>
        </td>
        <td style="border-top: 1px solid #000000; width: 120px; text-align: right;">
            <span style="font-weight: bold;">{{ number_format($debits,2) }}</span>
        </td>
      </tr>
      <tr valign="top" style="height:10px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
        <td>
            <span style="font-weight: bold;">LABA KOTOR</span>
        </td>
        <td style="border-top: 1px solid #000000;text-align: right;">
            <span style="font-weight: bold;">{{ number_format($debits,2) }}</span>
        </td>
      </tr>
      <tr valign="top" style="height:10px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
        <td colspan="2">
            <span style="font-weight: bold;">BIAYA</span>
        </td>
      </tr>
      @foreach ($losses as $loss)
      @php $credits += $loss->credit; @endphp
      @if (!$is_total)
      @if ($is_zero)
      <tr valign="top">
          <td>
              <span style="@if ($loss->parent < 1) font-weight: bold; @endif">
                  @if ($loss->parent > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;{{ $loss->name }}
                  @else
                      &nbsp; &nbsp;{{ $loss->name }}
                  @endif
              </span>
          </td>
          <td style="text-align: right;">
              <span style="@if ($loss->parent < 1) font-weight: bold; @endif">{{ number_format($loss->credit,2) }}</span>
          </td>
      </tr>
      @else
      @if ($loss->credit > 0)
      <tr valign="top">
          <td>
              <span style="@if ($loss->parent < 1) font-weight: bold; @endif">
                  @if ($loss->parent > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;{{ $loss->name }}
                  @else
                      &nbsp; &nbsp;{{ $loss->name }}
                  @endif
              </span>
          </td>
          <td style="text-align: right;">
              <span style="@if ($loss->parent < 1) font-weight: bold; @endif">{{ number_format($loss->credit,2) }}</span>
          </td>
      </tr>
      @endif
      @endif
      @endif
      @endforeach
      <tr valign="top" style="height:14px">
        <td>
            <span style="font-weight: bold;">Jumlah Biaya</span>
        </td>
        <td style="border-top: 1px solid #000000; width: 120px; text-align: right;">
            <span style="font-weight: bold;">{{ number_format($credits,2) }}</span>
        </td>
      </tr>
      <tr valign="top" style="height:10px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
        <td>
            <span style="font-weight: bold;">LABA BERSIH</span>
        </td>
        <td style="border-top: 1px solid #000000;border-bottom: 2px double #000000;text-align: right;">
            <span style="font-weight: bold;">{{ number_format($debits - $credits,2) }}</span>
        </td>
      </tr>
    </tbody>
  </table>
</body>
</html>