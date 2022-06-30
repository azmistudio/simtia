@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $total_income = 0;
  $total_receivable = 0;
  $total_receivable_reduce = 0;
  $total_equity = 0;
  $total_equity_withdrawal = 0;
  $total_investment = 0;
@endphp
<html>
<head>
  <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN ARUS KAS</title>
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
  <div class="text-center" style="font-size:16px;"><b>LAPORAN ARUS KAS</b></div>
  <br/>
  <br/>
  <table class="table no-border" style="font-size: 13px;font-weight:700">
      <tbody>
        <tr>
          <td style="width:12%;">Departemen</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:20%;">-- SEMUA --</td>
          <td style="width:12%;">Periode</td>
          <td style="width: 1%;text-align:center;">:</td>
          <td style="width:26%;">{{ $startdate }} - {{ $enddate }}</td>
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
          <td style="width:26%;">{{ auth()->user()->name }}</td>
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
              <span class="content-span" style="font-weight: bold;">Aktifitas Operasi</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;"></span>
          </td>
      </tr>
      @foreach ($incomes as $income)
      @php $total_income += $income->value; @endphp
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span">&nbsp;&nbsp;Kas diterima dari {{ $income->name }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">{{ number_format($income->value,2) }}</td>
      </tr>
      @endforeach
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span">&nbsp;&nbsp;Pembayaran Beban</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">{{ number_format($expense,2) }}</td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Arus Kas Bersih dari Aktifitas Operasi</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;border-top: solid 1px #000000;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($total_income + $expense,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Aktifitas Keuangan</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;"></span>
          </td>
      </tr>
      @foreach ($receivables as $receivable)
      @php $total_receivable += $receivable->value; @endphp
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;Penambahan Piutang Usaha</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($receivable->value,2) }}</span>
          </td>
      </tr>
      @endforeach
      @foreach ($receivables_reduce as $receivable)
      @php $total_receivable_reduce += $receivable->value; @endphp
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;Pengurangan Piutang Usaha</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($receivable->value,2) }}</span>
          </td>
      </tr>
      @endforeach
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;Penurunan Hutang</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($payable_reduce,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;Kenaikan Hutang</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($payable_raise,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Arus Kas Bersih dari Aktifitas Keuangan</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;border-top: solid 1px #000000;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Aktifitas Investasi</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;"></span>
          </td>
      </tr>
      @foreach ($equities as $equity)
      @php $total_equity += $equity->value; @endphp
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;Kas diterima dari penambahan {{ $equity->name }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($equity->value,2) }}</span>
          </td>
      </tr>
      @endforeach
      @foreach ($equities_withdrawal as $equity)
      @php $total_equity_withdrawal += $equity->value; @endphp
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;Pengurangan Kas dari pengambilan {{ $equity->name }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($equity->value,2) }}</span>
          </td>
      </tr>
      @endforeach
      @foreach ($investments as $investment)
      @php $total_investment += $investment->value; @endphp
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="">&nbsp;&nbsp;{{ $investment->name }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="">{{ number_format($investment->value,2) }}</span>
          </td>
      </tr>
      @endforeach
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Arus Kas Bersih dari Aktifitas Investasi</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;border-top: solid 1px #000000;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($total_equity + $total_equity_withdrawal + $total_investment,2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td colspan="2"> </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Perubahan Kas</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format(($total_income + $expense) + ($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise) + ($total_equity + $total_equity_withdrawal + $total_investment),2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Saldo Kas per {{ $start_date }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($begin_balance->value, 2) }}</span>
          </td>
      </tr>
      <tr valign="top" style="height:14px">
          <td class="content-td" style="width: 250px;">
              <span class="content-span" style="font-weight: bold;">Saldo Kas per {{ $end_date }}</span>
          </td>
          <td class="content-td" style="width: 120px;text-align: right;">
              <span class="content-span" style="font-weight: bold;">{{ number_format($begin_balance->value + ($total_income + $expense) + ($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise) + ($total_equity + $total_equity_withdrawal + $total_investment), 2) }}</span>
          </td>
      </tr>
    </tbody>
  </table>
</body>
</html>