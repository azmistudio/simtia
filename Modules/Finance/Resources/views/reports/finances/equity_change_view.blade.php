@php
    $subtotal = $equities[1]->value + $equities[2]->value + $equities[3]->value;
    $total = $equities[0]->value + $subtotal;
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
                                    <span style="color: #333; font-size: 14px; line-height: 1.1499023; font-weight: bold;">Perubahan Ekuitas Pemilik - {{ $requests['start'] }} s.d {{ $requests['end'] }}</span>
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
                                    <span class="content-span" style="font-weight: bold;">Ekuitas pemilik awal {{ $month }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($equities[0]->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Penambahan Ekuitas Pemilik</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;"> </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span">&nbsp;&nbsp;Pendapatan Bersih pada {{ $month }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span">{{ number_format($equities[1]->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span">&nbsp;&nbsp;Investasi Kurun Periode {{ $month }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span">{{ number_format($equities[2]->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span">&nbsp;&nbsp;Penarikan pada {{ $month }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span">{{ number_format($equities[3]->value,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Total Penambahan Ekuitas Pemilik</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;border-top: 1px solid #000000;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($subtotal,2) }}</span>
                                </td>
                                <td colspan="3"> </td>
                            </tr>
                            <tr valign="top" style="height:14px">
                                <td colspan="2"> </td>
                                <td class="content-td" style="width: 250px;">
                                    <span class="content-span" style="font-weight: bold;">Ekuitas pemilik per {{ $lastdate }}</span>
                                </td>
                                <td> </td>
                                <td class="content-td" style="width: 120px;text-align: right;">
                                    <span class="content-span" style="font-weight: bold;">{{ number_format($total,2) }}</span>
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