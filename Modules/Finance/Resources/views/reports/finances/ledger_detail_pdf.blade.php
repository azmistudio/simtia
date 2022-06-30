@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $balances = array();
@endphp
<html>
<head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - BUKU BESAR</title>
    <link href="file:///{{ public_path('css/report.css') }}" rel="stylesheet" />
    <style type="text/css">
      table { border-collapse: collapse; border: 1px solid #000; font-size:13px; page-break-inside: auto; }
      table.row > tbody > tr:nth-child(even) { background: #f5f5f5; }
      table.row > tbody > tr:nth-child(odd) { background: #fff; }
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
    <div class="text-center" style="font-size:16px;"><b>RINCIAN BUKU BESAR</b></div>
    <br/>
    <br/>
    <table class="table no-border" style="font-size: 13px;font-weight:700">
      <tbody>
        <tr>
          <td style="width:9%;">Departemen</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:29%;">-- SEMUA --</td>
          <td style="width:10%;">Periode Tanggal</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:30%;">{{ $requests['start'] }} s/d {{ $requests['end'] }}</td>
          <td style="width:9%;">Mata Uang</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:25%;">Rupiah (Rp)</td>
        </tr>
        <tr>
          <td style="width:9%;">Tahun Buku</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:29%;">{{ $requests['bookyear'] }}</td>
          <td style="width:10%;">Dicetak oleh</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:30%;">{{ auth()->user()->name }}</td>
          <td style="width:9%;">Tanggal Cetak</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:25%;">{{ date('d/m/Y H:i:s') }}</td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table class="table no-border row" style="font-size: 13px;width: 100%;">
        <thead>
            <tr>
                <th class="border-bottom text-left">Tanggal</th>
                <th class="border-bottom text-left">Tipe Transaksi</th>
                <th class="border-bottom text-left">Departemen</th>
                <th class="border-bottom text-left">Keterangan</th>
                <th class="border-bottom text-right">Debit</th>
                <th class="border-bottom text-right">Kredit</th>
                <th class="border-bottom text-right">Saldo Akhir</th>
            </tr>
        </thead>
        <tbody>
            <!-- account -->
            @foreach ($accounts as $account)
            <tr>
                <td colspan="7">
                    <span style="font-weight: bold;">{{ $account->code }} | {{ $account->name }}</span>
                </td>
            </tr>
            <!-- end balance -->
            @foreach ($end_balances as $end_balance)
            @if ($end_balance->account_id == $account->id)
            @php 
                $balances[] = array(
                    'account_id' => $end_balance->account_id, 
                    'end_balance'=> $end_balance->end_balance,
                ); 
            @endphp
            <tr valign="top">
                <td><span>&nbsp;&nbsp;{{ $balance_date }}</span></td>
                <td><span>Saldo per {{ $balance_date }}</span></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td style="text-align: right;"><span>{{ number_format($end_balance->end_balance,2) }}</span></td>
            </tr>
            @endif
            @endforeach
            <!-- transaction -->
            @foreach ($account_details as $account_detail)
            @if ($account_detail->account_id == $account->id)
            @foreach ($balances as $balance)
            @if ($balance['account_id'] == $account->id)
            <tr valign="top">
                <td style=""><span>&nbsp;&nbsp;{{ $account_detail->journal_date }}</span></td>
                <td style=""><span>{{ $account_detail->source }}</span></td>
                <td style=""><span>{{ $account_detail->department }}</span></td>
                <td style="white-space: pre-wrap;"><span>{{ $account_detail->remark }}</span></td>
                <td style="text-align: right;"><span>{{ number_format($account_detail->debit,2) }}</span></td>
                <td style="text-align: right;"><span>{{ number_format($account_detail->credit,2) }}</span></td>
                <td style="text-align: right;"> 
                    <span>
                        {{ number_format(($balance['end_balance'] + $account_detail->debit) - $account_detail->credit,2) }}
                    </span>
                </td>
            </tr>
            @endif
            @endforeach
            @endif
            @endforeach
            <!-- sub total -->
            @foreach ($subtotals as $total)
            @if ($total->account_id == $account->id)
            <tr valign="top">
                <td colspan="4"></td>
                <td class="border-top" style="text-align: right;"><span><b>{{ number_format($total->debit,2) }}</b></span></td>
                <td class="border-top" style="text-align: right;"><span><b>{{ number_format($total->credit,2) }}</b></span></td>
                <td></td>
            </tr>
            @endif
            @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>