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
    $subtotal_equity = (array_sum(array_column($equities, 'balance')) + $profit_loss);
    $total_liability = $subtotal_liability + $subtotal_equity;
@endphp
<html>
<head>
    <title></title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="{{ asset('css/report-preview.css') }}" rel="stylesheet" />
</head>
<body text="#000000" link="#000000" alink="#000000" vlink="#000000">
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td width="50%">&nbsp;</td>
                <td align="center">
                    <br>
                    <table cellpadding="0" cellspacing="0" border="0" style="empty-cells: show; width: 595px; border-collapse: collapse; background-color: white;">
                        <tbody>
                            <tr valign="top" style="height:0">
                                <td style="width:28px"></td>
                                <td style="width:74px"></td>
                                <td style="width:250px"></td>
                                <td style="width:10px"></td>
                                <td style="width:120px"></td>
                                <td style="width:35px"></td>
                                <td style="width:50px"></td>
                                <td style="width:28px"></td>
                            </tr>
                            <tr valign="top" style="height:28px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:17px">
                                <td> </td>
                                <td colspan="6" class="cell-title">
                                    <span style="font-size: 12px; line-height: 1.0078125;">{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</span>
                                </td>
                                <td> </td>
                            </tr>
                            <tr valign="top" style="height:23px">
                                <td> </td>
                                <td colspan="6" class="cell-title">
                                    <span style="color: #333; font-size: 14px; line-height: 1.1499023; font-weight: bold;">Neraca - per tanggal {{ $requests['end'] }}</span>
                                </td>
                                <td> </td>
                            </tr>
                            <tr valign="top" style="height:12px">
                                <td> </td>
                                <td colspan="6" class="cell-title">
                                    <span style="font-size: 10px; line-height: 1.0078125;">Tahun Buku {{ $requests['bookyear'] }} - Tercetak {{ date('d/m/Y H:i:s') }}</span>
                                </td>
                                <td> </td>
                            </tr>
                            <tr valign="top" style="height:20px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="header-title-td" style="width: 250px;">
                                    <span class="header-title">Deskripsi</span>
                                </td>
                                <td> </td>
                                <td class="header-title-td" style="width: 120px;text-align: right;">
                                    <span class="header-title">Nilai</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:5px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">HARTA</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;ASET LANCAR</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Kas dan Setara Kas</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @if (!$is_total)
                            @foreach ($cashbanks as $cashbank)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                                        @if ($cashbank['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                                        @if ($cashbank['parent'] < 1)
                                            {{ number_format($cashbank['balance_total'],2) }}
                                        @else
                                            {{ number_format($cashbank['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($cashbank['balance'] <> 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                                        @if ($cashbank['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $cashbank['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($cashbank['parent'] < 1) font-weight: bold; @endif">
                                        @if ($cashbank['parent'] < 1)
                                            {{ number_format($cashbank['balance_total'],2) }}
                                        @else
                                            {{ number_format($cashbank['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Kas dan Setara Kas</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_bank,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Piutang Usaha</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @if (!$is_total)
                            @foreach ($receivables as $receivable)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                                        @if ($receivable['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                                        @if ($receivable['parent'] < 1)
                                            {{ number_format($receivable['balance_total'],2) }}
                                        @else
                                            {{ number_format($receivable['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($receivable['balance'] <> 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                                        @if ($receivable['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $receivable['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($receivable['parent'] < 1) font-weight: bold; @endif">
                                        @if ($receivable['parent'] < 1)
                                            {{ number_format($receivable['balance_total'],2) }}
                                        @else
                                            {{ number_format($receivable['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Piutang Usaha</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_receivable,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;Jumlah Aset Lancar</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_bank + $subtotal_receivable,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;ASET TIDAK LANCAR</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Nilai Histori</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @if (!$is_total)
                            @foreach ($assets as $asset)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                                        @if ($asset['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                                        @if ($asset['parent'] < 1)
                                            {{ number_format($asset['balance_total'],2) }}
                                        @else
                                            {{ number_format($asset['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($asset['balance'] <> 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                                        @if ($asset['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $asset['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($asset['parent'] < 1) font-weight: bold; @endif">
                                        @if ($asset['parent'] < 1)
                                            {{ number_format($asset['balance_total'],2) }}
                                        @else
                                            {{ number_format($asset['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Nilai Histori</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_asset,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>

                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Akumulasi Penyusutan</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @if (!$is_total)
                            @foreach ($depretiations as $depretiation)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                                        @if ($depretiation['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                                        @if ($depretiation['parent'] < 1)
                                            {{ number_format($depretiation['balance_total'],2) }}
                                        @else
                                            {{ number_format($depretiation['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($depretiation['balance'] <> 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                                        @if ($depretiation['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $depretiation['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($depretiation['parent'] < 1) font-weight: bold; @endif">
                                        @if ($depretiation['parent'] < 1)
                                            {{ number_format($depretiation['balance_total'],2) }}
                                        @else
                                            {{ number_format($depretiation['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Akumulasi Penyusutan</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_depretiation,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;Jumlah Aset Tidak Lancar</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_asset + $subtotal_depretiation,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">JUMLAH HARTA</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($total_asset,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">KEWAJIBAN DAN EKUITAS</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;HUTANG</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @if (!$is_total)
                            @foreach ($liabilities as $liability)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                                        @if ($liability['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                                        @if ($liability['parent'] < 1)
                                            {{ number_format($liability['balance_total'],2) }}
                                        @else
                                            {{ number_format($liability['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($liability['balance'] <> 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                                        @if ($liability['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $liability['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($liability['parent'] < 1) font-weight: bold; @endif">
                                        @if ($liability['parent'] < 1)
                                            {{ number_format($liability['balance_total'],2) }}
                                        @else
                                            {{ number_format($liability['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Hutang Usaha</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_liability,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;MODAL</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @if (!$is_total)
                            @foreach ($equities as $equity)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                                        @if ($equity['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                                        @if ($equity['parent'] < 1)
                                            {{ number_format($equity['balance_total'],2) }}
                                        @else
                                            {{ number_format($equity['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($equity['balance'] <> 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                                        @if ($equity['parent'] > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                                        @else
                                            &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $equity['name'] }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($equity['parent'] < 1) font-weight: bold; @endif">
                                        @if ($equity['parent'] < 1)
                                            {{ number_format($equity['balance_total'],2) }}
                                        @else
                                            {{ number_format($equity['balance'],2) }}
                                        @endif
                                    </span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endforeach
                            @endif
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Laba Tahun ini</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($profit_loss,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">&nbsp;&nbsp;&nbsp;&nbsp;Jumlah Ekuitas</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($subtotal_equity,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">JUMLAH KEWAJIBAN DAN EKUITAS</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000; border-bottom: 2px double #000000;text-align: right;">
                                    <span class="content-span" style="font-weight:bold;">{{ number_format($total_liability,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:28px">
                                <td colspan="8"> </td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                </td>
                <td width="50%">&nbsp;</td>
            </tr>
        </tbody>
    </table>
</body>
</html>