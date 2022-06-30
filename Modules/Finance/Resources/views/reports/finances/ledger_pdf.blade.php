@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
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
	<div class="text-center" style="font-size:16px;"><b>RINGKASAN BUKU BESAR</b></div>
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
				<th class="border-bottom text-left">Kode</th>
				<th class="border-bottom text-left">Nama</th>
				<th class="border-bottom text-right">Saldo Awal</th>
				<th class="border-bottom text-right">Perubahan Debit</th>
				<th class="border-bottom text-right">Perubahan Kredit</th>
				<th class="border-bottom text-right">Saldo Akhir</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($accounts as $account)

            @if ($balance) 
                @if ($account->beg_balance > 0 || $account->trx_debit > 0 || $account->trx_credit > 0 || $account->end_balance > 0) 
                <tr valign="top">
                    <td>
                        <span style="@if ($account->parent < 1) font-weight: bold; @endif">
                        @if ($account->parent > 0) 
                            &nbsp;&nbsp;{{ $account->code }}
                        @else
                            {{ $account->code }}
                        @endif
                        </span>
                    </td>
                    <td><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ $account->name }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->beg_balance,2) }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_debit,2) }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_credit,2) }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->end_balance,2) }}</span></td>
                </tr>
                @endif
            @else
                <tr valign="top">
                    <td>
                        <span style="@if ($account->parent < 1) font-weight: bold; @endif">
                        @if ($account->parent > 0) 
                            &nbsp;&nbsp;{{ $account->code }}
                        @else
                            {{ $account->code }}
                        @endif
                        </span>
                    </td>
                    <td><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ $account->name }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->beg_balance,2) }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_debit,2) }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->trx_credit,2) }}</span></td>
                    <td class="text-right"><span style="@if ($account->parent < 1) font-weight: bold; @endif">{{ number_format($account->end_balance,2) }}</span></td>
                </tr>
            @endif
            
            @endforeach
		</tbody>
	</table>
</body>
</html>