@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@php
  $totalPaymentMajor = 0;
  $totalPaymentReceipt = 0;
  $totalPaymentDiscount = 0;
  $totalPaymentRemain = 0;
  $totalPaymentVoluntary = 0;
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="4" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="4" align="center" class="title"><b>LAPORAN PEMBAYARAN PER SANTRI</b></td></tr>
        <tr><td colspan="4" align="center" class="subtitle"><b>DEPARTEMEN {{ $payloads->department }} - TAHUN AJARAN {{ $payloads->schoolyear }} - TINGKAT {{ $payloads->grade }}</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table class="no-border">
      <tbody>
        <tr>
          <td>Santri</td>
          <td>: <b>{{ $payloads->student_no .' - '. $payloads->student }}</b></td>
        </tr>
        <tr>
          <td>Kelas</td>
          <td>: <b>{{ $payloads->class }}</b></td>
        </tr>
        <tr>
          <td>Tanggal</td>
          <td>: <b>{{ $payloads->start_date }} s.d {{ $payloads->end_date }}</b></td>
        </tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
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
          <td colspan="4" bgcolor="#CCCFFF"><b>{{ $mandatory->getReceipt->name . ' Periode ' . $mandatory->period }}</b></td>
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
                No. Jurnal: <b>{{ $receiptMajorLast[0]->getPaymentMajor->getBookYear->book_year . $receiptMajorLast[0]->getJournal->cash_no }}</b>
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
    <div><b>REKAPITULASI PEMBAYARAN</b></div>
    <table width="100%" class="">
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
  </body>
</html>