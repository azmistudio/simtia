@php
    $total_income = 0;
    $total_receivable = 0;
    $total_receivable_reduce = 0;
    $total_equity = 0;
    $total_equity_withdrawal = 0;
    $total_investment = 0;
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
                                    <span style="color: #333; font-size: 14px; line-height: 1.1499023; font-weight: bold;">Arus Kas - {{ $requests['start'] }} s.d {{ $requests['end'] }}</span>
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
                                    <span class="content-span" style="font-weight: bold;">Aktifitas Operasi</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;"></span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @foreach ($incomes as $income)
                            @php $total_income += $income->value; @endphp
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Kas diterima dari {{ $income->name }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($income->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endforeach
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Pembayaran Beban</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($expense,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Arus Kas Bersih dari Aktifitas Operasi</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;border-top: solid 1px #000000;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($total_income + $expense,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:7px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Aktifitas Keuangan</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;"></span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @foreach ($receivables as $receivable)
                            @php $total_receivable += $receivable->value; @endphp
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Penambahan Piutang Usaha</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($receivable->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endforeach

                            @foreach ($receivables_reduce as $receivable)
                            @php $total_receivable_reduce += $receivable->value; @endphp
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Pengurangan Piutang Usaha</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($receivable->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endforeach
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Penurunan Hutang</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($payable_reduce,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Kenaikan Hutang</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($payable_raise,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Arus Kas Bersih dari Aktifitas Keuangan</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;border-top: solid 1px #000000;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:7px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Aktifitas Investasi</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;"></span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @foreach ($equities as $equity)
                            @php $total_equity += $equity->value; @endphp
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Kas diterima dari penambahan {{ $equity->name }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($equity->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endforeach
                            @foreach ($equities_withdrawal as $equity)
                            @php $total_equity_withdrawal += $equity->value; @endphp
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;Pengurangan Kas dari pengambilan {{ $equity->name }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($equity->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endforeach
                            @foreach ($investments as $investment)
                            @php $total_investment += $investment->value; @endphp
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="">&nbsp;&nbsp;{{ $investment->name }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="">{{ number_format($investment->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endforeach
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Arus Kas Bersih dari Aktifitas Investasi</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;border-top: solid 1px #000000;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($total_equity + $total_equity_withdrawal + $total_investment,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:7px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Perubahan Kas</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format(($total_income + $expense) + ($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise) + ($total_equity + $total_equity_withdrawal + $total_investment),2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Saldo Kas per {{ $start_date }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($begin_balance->value, 2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Saldo Kas per {{ $end_date }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($begin_balance->value + ($total_income + $expense) + ($total_receivable + $total_receivable_reduce + $payable_reduce + $payable_raise) + ($total_equity + $total_equity_withdrawal + $total_investment), 2) }}</span>
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