@php
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
    <title></title>
    <style type="text/css">
      body { margin: 0; padding: 0; font-family: "Calibri", "Open Sans", serif !important; }
      .text-left {text-align: left;}
      .text-center {text-align: center;}
      .text-right {text-align: right;}
      table.no-border, table.no-border th, table.no-border td {border: none;}
      table {border-collapse: collapse;border: 1px solid #000;font-size:13px;page-break-inside: auto;}
      th, td {border: 1px solid #000;padding: 2px;}
      .title { font-size: 13pt; font-weight: bold; }
      .subtitle { font-size: 11pt; font-weight: bold; }
    </style>
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="8" align="center" class="title">{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</td></tr>
        <tr><td colspan="8" align="center" class="title">LAPORAN NERACA SAMPAI DENGAN {{ $requests['end'] }}</td></tr>
        <tr><td colspan="8" align="center" class="subtitle">TAHUN BUKU {{ $requests['bookyear'] }} - MATA UANG RUPIAH (Rp)</td></tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;">
      <thead>
        <tr>
          <th colspan="7" class="border-bottom text-left">Deskripsi</th>
          <th class="border-bottom text-right">Nilai</th>
        </tr>
      </thead>
      <tbody>
        <tr valign="top">
            <td colspan="7"><span style="font-weight: bold;">HARTA</span></td>
            <td></td>
        </tr>
        <tr valign="top">
            <td colspan="7"><span style="font-weight: bold;">&nbsp;&nbsp;ASET LANCAR</span></td>
            <td></td>
        </tr>
        <tr valign="top">
            <td colspan="7"><span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Kas dan Setara Kas</span></td>
            <td></td>
        </tr>
        @if (!$is_total)
        @foreach ($cashbanks as $cashbank)
        @if ($is_zero)
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                    @if ($cashbank['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                    @if ($cashbank['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Kas dan Setara Kas</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_bank,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Piutang Usaha</span>
            </td>
            <td></td>
        </tr>
        @if (!$is_total)
        @foreach ($receivables as $receivable)
        @if ($is_zero)
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                    @if ($receivable['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                    @if ($receivable['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Piutang Usaha</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_receivable,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;Jumlah Aset Lancar</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_bank + $subtotal_receivable,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;ASET TIDAK LANCAR</span>
            </td>
            <td></td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Nilai Histori</span>
            </td>
            <td></td>
        </tr>
        @if (!$is_total)
        @foreach ($assets as $asset)
        @if ($is_zero)
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                    @if ($asset['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                    @if ($asset['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Nilai Histori</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_asset,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>

        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Akumulasi Penyusutan</span>
            </td>
            <td></td>
        </tr>
        @if (!$is_total)
        @foreach ($depretiations as $depretiation)
        @if ($is_zero)
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                    @if ($depretiation['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                    @if ($depretiation['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Akumulasi Penyusutan</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_depretiation,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;Jumlah Aset Tidak Lancar</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_asset + $subtotal_depretiation,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">JUMLAH HARTA</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($total_asset,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">KEWAJIBAN DAN EKUITAS</span>
            </td>
            <td></td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;HUTANG</span>
            </td>
            <td></td>
        </tr>
        @if (!$is_total)
        @foreach ($liabilities as $liability)
        @if ($is_zero)
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                    @if ($liability['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                    @if ($liability['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Hutang Usaha</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_liability,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;MODAL</span>
            </td>
            <td></td>
        </tr>
        @if (!$is_total)
        @foreach ($equities as $equity)
        @if ($is_zero)
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                    @if ($equity['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                    @if ($equity['parent'] > 0) 
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                    @else
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                    @endif
                </span>
            </td>
            <td style="text-align: right;">
                <span style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
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
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laba Tahun ini</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight: bold;">{{ number_format($profit_loss,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Ekuitas</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($subtotal_equity,2) }}</span>
            </td>
        </tr>
        <tr valign="top">
            <td colspan="7"> </td>
        </tr>
        <tr valign="top">
            <td colspan="7">
                <span style="font-weight: bold;">JUMLAH KEWAJIBAN DAN EKUITAS</span>
            </td>
            <td style="text-align: right;">
                <span style="font-weight:bold;">{{ number_format($total_liability,2) }}</span>
            </td>
        </tr>
        <tr valign="top" style="height:28px">
            <td colspan="7"> </td>
        </tr>
      </tbody>
      </table>
  </body>
</html>