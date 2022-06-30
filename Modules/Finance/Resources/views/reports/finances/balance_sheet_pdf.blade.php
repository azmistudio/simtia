@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $subtotal_bank = array_sum(array_column($cashbanks, 'balance'));
  $subtotal_receivable = array_sum(array_column($receivables, 'balance'));
  $subtotal_asset = array_sum(array_column($assets, 'balance'));
  $subtotal_depretiation = array_sum(array_column($depretiations, 'balance'));
  $total_asset = $subtotal_bank + $subtotal_receivable + $subtotal_asset + $subtotal_depretiation;
  $subtotal_liability = array_sum(array_column($liabilities, 'balance'));
  $profit_val = 0;
  foreach ($profits as $profit)
  {
      if ($profit->parent > 0)
      {
          $profit_val += $profit->debit;
      }
  }
  $loss_val = 0;
  foreach ($losses as $loss)
  {
      if ($loss->parent > 0)
      {
          $loss_val += $loss->credit;
      }
  }
  $profit_loss = $profit_val - $loss_val;
  $subtotal_equity = array_sum(array_column($equities, 'balance')) + $profit_loss;
  $total_liability = $subtotal_liability + $subtotal_equity;
@endphp
<html>
<head>
  <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN LABA/RUGI</title>
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
  <div class="text-center" style="font-size:16px;"><b>NERACA STANDAR</b></div>
  <br/>
  <br/>
  <table class="table no-border" style="font-size: 13px;font-weight:700">
      <tbody>
        <tr>
          <td style="width:12%;">Departemen</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:20%;">-- SEMUA --</td>
          <td style="width:12%;">Sampai Tanggal</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:25%;">{{ $requests['end'] }}</td>
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
        <th class="border-bottom text-right">Nilai</th>
      </tr>
    </thead>
    <tbody>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">HARTA</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;ASET LANCAR</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Kas dan Setara Kas</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      @if (!$is_total)
      @foreach ($cashbanks as $cashbank)
      @if ($is_zero)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                  @if ($cashbank['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                  @if ($cashbank['parent'] < 1)
                      {{ number_format($cashbank['balance_total'],2) }}
                  @else
                      {{ number_format($cashbank['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @else
      @if ($cashbank['balance'] <> 0)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                  @if ($cashbank['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                  @if ($cashbank['parent'] < 1)
                      {{ number_format($cashbank['balance_total'],2) }}
                  @else
                      {{ number_format($cashbank['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @endif
      @endif
      @endforeach
      @endif
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Kas dan Setara Kas</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_bank,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Piutang Usaha</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      @if (!$is_total)
      @foreach ($receivables as $receivable)
      @if ($is_zero)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                  @if ($receivable['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                  @if ($receivable['parent'] < 1)
                      {{ number_format($receivable['balance_total'],2) }}
                  @else
                      {{ number_format($receivable['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @else
      @if ($receivable['balance'] <> 0)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                  @if ($receivable['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                  @if ($receivable['parent'] < 1)
                      {{ number_format($receivable['balance_total'],2) }}
                  @else
                      {{ number_format($receivable['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @endif
      @endif
      @endforeach
      @endif
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Piutang Usaha</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_receivable,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;Jumlah Aset Lancar</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_bank + $subtotal_receivable,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;ASET TIDAK LANCAR</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Nilai Histori</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      @if (!$is_total)
      @foreach ($assets as $asset)
      @if ($is_zero)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                  @if ($asset['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                  @if ($asset['parent'] < 1)
                      {{ number_format($asset['balance_total'],2) }}
                  @else
                      {{ number_format($asset['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @else
      @if ($asset['balance'] <> 0)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                  @if ($asset['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                  @if ($asset['parent'] < 1)
                      {{ number_format($asset['balance_total'],2) }}
                  @else
                      {{ number_format($asset['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @endif
      @endif
      @endforeach
      @endif
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Nilai Histori</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_asset,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>

      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Akumulasi Penyusutan</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      @if (!$is_total)
      @foreach ($depretiations as $depretiation)
      @if ($is_zero)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                  @if ($depretiation['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                  @if ($depretiation['parent'] < 1)
                      {{ number_format($depretiation['balance_total'],2) }}
                  @else
                      {{ number_format($depretiation['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @else
      @if ($depretiation['balance'] <> 0)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                  @if ($depretiation['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                  @if ($depretiation['parent'] < 1)
                      {{ number_format($depretiation['balance_total'],2) }}
                  @else
                      {{ number_format($depretiation['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @endif
      @endif
      @endforeach
      @endif
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Akumulasi Penyusutan</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_depretiation,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;Jumlah Aset Tidak Lancar</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_asset + $subtotal_depretiation,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">JUMLAH HARTA</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($total_asset,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">KEWAJIBAN DAN EKUITAS</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;HUTANG</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      @if (!$is_total)
      @foreach ($liabilities as $liability)
      @if ($is_zero)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                  @if ($liability['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                  @if ($liability['parent'] < 1)
                      {{ number_format($liability['balance_total'],2) }}
                  @else
                      {{ number_format($liability['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @else
      @if ($liability['balance'] <> 0)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                  @if ($liability['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                  @if ($liability['parent'] < 1)
                      {{ number_format($liability['balance_total'],2) }}
                  @else
                      {{ number_format($liability['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @endif
      @endif
      @endforeach
      @endif
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Hutang Usaha</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_liability,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;MODAL</span>
          </td>
          <td class="content-td" style="width: 120px;"> </td>
      </tr>
      @if (!$is_total)
      @foreach ($equities as $equity)
      @if ($is_zero)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                  @if ($equity['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                  @if ($equity['parent'] < 1)
                      {{ number_format($equity['balance_total'],2) }}
                  @else
                      {{ number_format($equity['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @else
      @if ($equity['balance'] <> 0)
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                  @if ($equity['parent'] > 0) 
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                  @else
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                  @endif
              </span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                  @if ($equity['parent'] < 1)
                      {{ number_format($equity['balance_total'],2) }}
                  @else
                      {{ number_format($equity['balance'],2) }}
                  @endif
              </span>
          </td>
      </tr>
      @endif
      @endif
      @endforeach
      @endif
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laba Tahun ini</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($profit_loss,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Ekuitas</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_equity,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="8"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">JUMLAH KEWAJIBAN DAN EKUITAS</span>
          </td>
          <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
              <span class="content-span" style="font-weight:bold;">{{ number_format($total_liability,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:28px">
          <td colspan="8"> </td>
      </tr>
    </tbody>
  </table>
</body>
</html>