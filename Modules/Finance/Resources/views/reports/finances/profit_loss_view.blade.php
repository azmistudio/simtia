@php
    $debits = 0;
    $credits = 0;
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
                                    <span style="color: #333; font-size: 14px; line-height: 1.1499023; font-weight: bold;">Laba/Rugi - {{ $requests['start'] }} s.d {{ $requests['end'] }}</span>
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
                                    <span class="header-title">{{ $startdate }} - {{ $lastdate }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:5px">
                                <td colspan="8"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">PENDAPATAN</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @foreach ($profits as $profit)
                            @php if ($profit->parent > 0) { $debits += $profit->debit; } @endphp
                            @if (!$is_total)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($profit->parent < 1) font-weight: bold; @endif">
                                        @if ($profit->parent > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $profit->name }}
                                        @else
                                            &nbsp; &nbsp;{{ $profit->name }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($profit->parent < 1) font-weight: bold; @endif">{{ number_format($profit->debit,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($profit->debit > 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($profit->parent < 1) font-weight: bold; @endif">
                                        @if ($profit->parent > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $profit->name }}
                                        @else
                                            &nbsp; &nbsp;{{ $profit->name }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($profit->parent < 1) font-weight: bold; @endif">{{ number_format($profit->debit,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Jumlah Pendapatan</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="border-top: 1px solid #000000; width: 120px; text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($debits,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;"> </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">LABA KOTOR</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="border-top: 1px solid #000000;width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($debits,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;"> </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">BIAYA</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            @foreach ($losses as $loss)
                            @php if ($loss->parent > 0) { $credits += $loss->credit; } @endphp
                            @if (!$is_total)
                            @if ($is_zero)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($loss->parent < 1) font-weight: bold; @endif">
                                        @if ($loss->parent > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $loss->name }}
                                        @else
                                            &nbsp; &nbsp;{{ $loss->name }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($loss->parent < 1) font-weight: bold; @endif">{{ number_format($loss->credit,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            @else
                            @if ($loss->credit != 0)
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="@if ($loss->parent < 1) font-weight: bold; @endif">
                                        @if ($loss->parent > 0) 
                                            &nbsp;&nbsp;&nbsp;&nbsp;{{ $loss->name }}
                                        @else
                                            &nbsp; &nbsp;{{ $loss->name }}
                                        @endif
                                    </span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="@if ($loss->parent < 1) font-weight: bold; @endif">{{ number_format($loss->credit,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>   
                            @endif
                            @endif
                            @endif
                            @endforeach
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Jumlah Biaya</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="border-top: 1px solid #000000; width: 120px; text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($credits,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;"> </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">LABA BERSIH</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="border-top: 1px solid #000000; border-bottom: 2px double #000000; width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($debits - $credits,2) }}</span>
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