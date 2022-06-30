@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@php
  $logo = strpos($profile['logo'], 'img') > 0 ? str_replace(url(''), base_path().'/public', $profile['logo']) : str_replace(url('').'/storage', base_path().'/storage/app/public/', $profile['logo']); 
  $totalPaymentMajor = 0;
  $totalPaymentReceipt = 0;
  $totalPaymentDiscount = 0;
  $totalPaymentRemain = 0;
  $totalPaymentVoluntary = 0;
@endphp
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - LAPORAN PEMBAYARAN PER SANTRI</title>
    <link href="file:///{{ public_path('css/report.css') }}" rel="stylesheet" />
    <style type="text/css">
      table { border-collapse: collapse; border: 1px solid #000; font-size:13px; page-break-inside: auto; }
      table.row > tbody > tr:nth-child(even) { background: #f5f5f5; }
      table.row > tbody > tr:nth-child(odd) { background: #fff; }
    </style>
  </head>
  <body>
    <div id="header">
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
    <div id="body">
      <br/>
      <div class="text-center" style="font-size:16px;"><b>LAPORAN PEMBAYARAN PER SANTRI</b></div>
      <br/>
      <br/>
      <div>
        <table class="table no-border" style="font-size: 13px;font-weight:700">
          <tbody>
            <tr>
              <td style="width:15%;">Departemen</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->department }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Tahun Ajaran</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->schoolyear }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Tingkat/Kelas</td>
              <td style="width:1%;text-align:center;">:</td>
              <td>{{ $requests->grade }} / {{ $requests->class }}</td>
            </tr>
            <tr>
              <td style="width:15%;">Santri</td>
              <td style="width:1%;text-align:center;">:</td>
              <td><b>{{ $requests->student_no .' - '. $requests->student }}</b></td>
            </tr>
          </tbody>
        </table>
        <br/>
        <div>
          <table border="1" style="width:100%;border-collapse:collapse">
            <tbody>
              {{-- mandatory --}}
              @foreach ($mandatories as $mandatory)
              @php 
                $receiptMajorTotal = $receiptMajorEloquent->totalPaymentReceipt($mandatory->id); 
                $receiptMajorLast = $receiptMajorEloquent->lastPaymentReceipt($mandatory->id);
                $remain = $mandatory->amount - ($receiptMajorTotal->total_receipt + $receiptMajorTotal->total_discount);
                $totalPaymentMajor += $mandatory->amount;
                $totalPaymentReceipt += ($receiptMajorTotal->total_receipt + $receiptMajorTotal->total_discount);
                $totalPaymentDiscount += $receiptMajorTotal->total_discount;
                $totalPaymentRemain += $remain;
              @endphp
              <tr height="35">
                <td colspan="4" bgcolor="#CCCFFF"><b>{{ $mandatory->getReceipt->name }}</b></td>
              </tr>
              <tr height="25">
                <td width="20%" bgcolor="#CCFFFF"><strong>Total Bayaran</strong></td>
                    <td width="15%" bgcolor="#FFFFFF" align="right">Rp{{ number_format($mandatory->amount,2) }}</td>
                    <td width="22%" bgcolor="#CCFFFF" align="center"><strong>Pembayaran Terakhir</strong></td>
                    <td width="43%" bgcolor="#CCFFFF" align="center"><strong>Keterangan</strong></td>
              </tr>
              <tr height="25">
                    <td bgcolor="#CCFFFF"><strong>Jumlah Besar Pembayaran</strong></td>
                    <td bgcolor="#FFFFFF" align="right">Rp{{ number_format($receiptMajorTotal->total_receipt + $receiptMajorTotal->total_discount, 2) }}</td>
                    <td bgcolor="#FFFFFF" align="center" valign="top" rowspan="3">
                      Rp{{ number_format($receiptMajorLast[0]->total,2) }}<br/>
                      Tanggal: {{ $receiptMajorLast[0]->trans_date }}<br/>
                      Diskon: Rp{{ $receiptMajorLast[0]->discount_amount }}<br/>
                      No. Jurnal: {{ $receiptMajorLast[0]->getJournal->cash_no }}
                    </td>
                    <td bgcolor="#FFFFFF" align="left" valign="top" rowspan="3">{{ $mandatory->remark }}</td>
                </tr>
                <tr height="25">
                    <td bgcolor="#CCFFFF"><strong>Jumlah Diskon</strong> </td>
                    <td bgcolor="#FFFFFF" align="right">Rp{{ number_format($receiptMajorTotal->total_discount,2) }}</td>
                </tr>
                <tr height="25">
                    <td bgcolor="#CCFFFF"><strong>Sisa Bayaran</strong> </td>
                    <td bgcolor="#FFFFFF" align="right">Rp{{ number_format($remain,2) }}</td>
                </tr>
                <tr height="3">
                    <td colspan="4" bgcolor="#E8E8E8">&nbsp;</td>
                </tr>
              @endforeach
              {{-- voluntary --}}
              @foreach ($voluntaries as $voluntary)
              @php 
                $receiptVoluntaryTotal = $receiptVoluntaryEloquent->totalPaymentReceipt($voluntary->receipt_id,$voluntary->student_id,0); 
                $receiptVoluntaryLast = $receiptVoluntaryEloquent->lastPaymentReceipt($voluntary->receipt_id,$voluntary->student_id,0);
                $totalPaymentVoluntary += $receiptVoluntaryTotal->total_receipt;
              @endphp
              <tr height="35">
                <td colspan="4" bgcolor="#CCCFFF"><b>{{ $voluntary->getReceipt->name }}</b></td>
              </tr>
              <tr height="25">
                    <td width="22%" bgcolor="#CCFFFF" align="center"><strong>Total Pembayaran</strong> </td>
                    <td width="22%" bgcolor="#CCFFFF" align="center"><strong>Pembayaran Terakhir</strong></td>
                    <td width="50%" colspan="2" bgcolor="#CCFFFF" align="center"><strong>Keterangan</strong></td>
                </tr>
                <tr height="25">
                    <td bgcolor="#FFFFFF" align="center">Rp{{ number_format($receiptVoluntaryTotal->total_receipt) }}</td>
                    <td bgcolor="#FFFFFF" align="center">
                      Rp{{ isset($receiptVoluntaryLast[0]) ? number_format($receiptVoluntaryLast[0]->total,2) : 0 }}<br/>
                      Tanggal: {{ isset($receiptVoluntaryLast[0]) ? $receiptVoluntaryLast[0]->trans_date : '-' }}<br/>
                    </td>
                    <td colspan="2" bgcolor="#FFFFFF" align="left">&nbsp;</td>
                </tr>
                <tr height="3">
                    <td colspan="4" bgcolor="#E8E8E8">&nbsp;</td>
                </tr>
              @endforeach
            </tbody>
          </table>
          <br/>
          <h3>REKAPITULASI PEMBAYARAN</h3>
        </div>
        <div>
          <table width="100%" class="table no-border">
            <tr>
              <td width="50%" align="left" valign="top">
                <table border="1" style="width:100%;border-collapse:collapse" cellpadding="5">
                  <tbody>
                    <tr>
                      <td colspan="2" bgcolor="#87C7F4" style="font-size:14px;"><b>Iuran Wajib Santri</b></td>
                    </tr>
                    <tr>
                      <td bgcolor="#E6F5FF">Total Semua Besar Bayaran</td>
                      <td class="text-right">Rp{{ number_format($totalPaymentMajor,2) }}</td>
                    </tr>
                    <tr>
                      <td bgcolor="#E6F5FF">Total Semua Pembayaran</td>
                      <td class="text-right">Rp{{ number_format($totalPaymentReceipt,2) }}</td>
                    </tr>
                    <tr>
                      <td bgcolor="#E6F5FF">Total Semua Diskon</td>
                      <td class="text-right">Rp{{ number_format($totalPaymentDiscount,2) }}</td>
                    </tr>
                    <tr>
                      <td bgcolor="#E6F5FF">Total Semua Sisa Tagihan</td>
                      <td class="text-right">Rp{{ number_format($totalPaymentRemain,2) }}</td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td width="50%" align="left" valign="top">
                <table border="1" style="width:100%;border-collapse:collapse" cellpadding="5">
                  <tbody>
                    <tr>
                      <td colspan="2" bgcolor="#87C7F4" style="font-size:14px;"><b>Iuran Sukarela Santri</b></td>
                    </tr>
                      <tr>
                        <td bgcolor="#E6F5FF">Total Semua Pembayaran</td>
                        <td class="text-right">Rp{{ number_format($totalPaymentVoluntary,2) }}</td>
                      </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </table>
        </div>
      </div>
  </body>
</html>