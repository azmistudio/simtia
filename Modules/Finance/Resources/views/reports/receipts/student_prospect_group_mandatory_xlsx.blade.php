@inject('receiptMajorEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptMajorEloquent')
@php
  $colspan = 4 + $max_installment;
  $totalPayment = 0;
  $totalMajor = 0;
  $totalDiscount = 0;
  $totalArrear = 0;
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="{{ $colspan + 4 }}" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="{{ $colspan + 4 }}" align="center" class="title"><b>LAPORAN PEMBAYARAN PER KELOMPOK CALON SANTRI - {{ $payloads->payment }}</b></td></tr>
        <tr><td colspan="{{ $colspan + 4 }}" align="center" class="subtitle"><b>DEPARTEMEN {{ $payloads->department }} - KELOMPOK {{ $payloads->prospect_group }}</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <thead>
        <tr style="background-color:#CCFFFF;">
          <th class="subtitle">No.</th>
          <th class="subtitle">No.Reg.</th>
          <th class="subtitle">Nama</th>
          @for ($i = 0;$i < $max_installment; $i++)
          <th class="subtitle">Bayaran-{{ $i + 1 }}</th>
          @endfor
          <th class="subtitle">Status</th>
          <th class="subtitle" width="15%">{{ $payloads->payment }}</th>
          <th class="subtitle">Total<br/>Besar Pembayaran</th>
          <th class="subtitle">Total<br/>Diskon</th>
          <th class="subtitle" width="15%">Total<br/>Tunggakan</th>
          <th class="subtitle" width="10%">Keterangan</th>
        </tr>
      </thead>
      <tbody>
        @php $x = 1; @endphp
        @foreach ($payments as $pay) 
        <tr>
          <td class="text-center">{{ $x }}</td>
          <td class="text-center">{{ $pay->registration_no }}</td>
          <td>{{ $pay->student }}</td>
          @php $receipts = $receiptMajorEloquent->listPaymentProspectGroup($payloads->department_id, $pay->prospect_student_id, $payloads->status); @endphp
          @if (count($receipts['queries']) > 0)
          @foreach ($receipts['queries'] as $receipt) 
          <td>
            <table style="width: 100%;">
              <tbody>
                <tr>
                  <td class="text-right">Rp{{ number_format($receipt->total,2) }}</td>
                </tr>
                <tr>
                  <td class="text-right">{{ $receipt->tdate }}</td>
                </tr>
              </tbody>
            </table>
          </td>
          @endforeach
          @if (count($receipts['queries']) < $max_installment)
              @for ($i = 0;$i < $max_installment - count($receipts['queries']); $i++)
                <td></td>
              @endfor
              @endif
          @else
          @for ($i = 0;$i < $max_installment; $i++)
            <td></td>
          @endfor
          @endif
          <td class="text-center">{{ $pay->status }}</td>
          <td class="text-right">Rp{{ number_format($pay->amount,2) }}</td>
          <td class="text-right">Rp{{ number_format($receipts['major'],2) }}</td>
          <td class="text-right">Rp{{ number_format($receipts['discount'],2) }}</td>
          <td class="text-right">Rp{{ number_format($pay->amount - $receipts['major'],2) }}</td>
          <td>{{ $pay->remark }}</td>
        </tr>
        @php 
          $x++; 
          $totalPayment += $pay->amount;
          $totalMajor += $receipts['major'];
          $totalDiscount += $receipts['discount'];
        @endphp
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="{{ $colspan }}" class="text-center"><b>TOTAL</b></td>
          <td class="text-right" width="15%"><b>Rp{{ number_format($totalPayment,2) }}</b></td>
          <td class="text-right"><b>Rp{{ number_format($totalMajor,2) }}</b></td>
          <td class="text-right"><b>Rp{{ number_format($totalDiscount,2) }}</b></td>
          <td class="text-right" width="15%"><b>Rp{{ number_format($totalPayment - $totalMajor,2) }}</b></td>
          <td></td>
        </tr>
      </tfoot>
      </table>
  </body>
</html>