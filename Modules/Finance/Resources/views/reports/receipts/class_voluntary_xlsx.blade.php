@inject('receiptVoluntaryEloquent', 'Modules\Finance\Repositories\Receipt\ReceiptVoluntaryEloquent')
@php
  $colspan = 4 + intval($max_installment);
  $totalMajor = 0;
@endphp
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="{{ $colspan + 1 }}" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="{{ $colspan + 1 }}" align="center" class="title"><b>LAPORAN PEMBAYARAN PER KELAS - {{ $payloads->payment }}</b></td></tr>
        <tr><td colspan="{{ $colspan + 1 }}" align="center" class="subtitle"><b>DEPARTEMEN {{ $payloads->department }} - TAHUN AJARAN {{ $payloads->schoolyear }} - TINGKAT/SEMESTER {{ $payloads->grade }}</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <thead>
        <tr style="background-color:#CCFFFF;">
          <th class="subtitle">No.</th>
          <th class="subtitle">NIS</th>
          <th class="subtitle">Nama</th>
          <th class="subtitle">Kelas</th>
          @for ($i = 0;$i < $max_installment; $i++)
          <th class="subtitle">Bayaran-{{ $i + 1 }}</th>
          @endfor
          <th class="subtitle">Total<br/>Pembayaran</th>
        </tr>
      </thead>
      <tbody>
        @php $x = 1; @endphp
        @foreach ($payments as $pay) 
        <tr>
          <td class="text-center">{{ $x }}</td>
          <td class="text-center">{{ $pay->student_no }}</td>
          <td>{{ $pay->student }}</td>
          <td>{{ $payloads->class }}</td>
          @php $receipts = $receiptVoluntaryEloquent->listPayment($payloads->bookyear_id, $payloads->department_id, $payloads->class_id, $pay->student_id, 0); @endphp
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
          <td class="text-right">Rp{{ number_format($receipts['major'],2) }}</td>
        </tr>
        @php 
          $x++; 
          $totalMajor += $receipts['major'];
        @endphp
        @endforeach
      </tbody>
      <tfoot>
        <tr>
          <td colspan="{{ $colspan }}" class="text-center"><b>TOTAL</b></td>
          <td class="text-right"><b>Rp{{ number_format($totalMajor,2) }}</b></td>
        </tr>
      </tfoot>
      </table>
  </body>
</html>