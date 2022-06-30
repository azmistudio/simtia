@inject('reference', 'Modules\Finance\Http\Controllers\ReceiptTypeController')
<html>
  <head>
    <title>{{ config('app.name') .' '. Session::get('institute') }} - Data Jenis Penerimaan</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Jenis Penerimaan - {{ config('app.name') }}</span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-left">NAMA DEPARTEMEN</th>
            <th class="text-left">KATEGORI</th>
            <th class="text-left">NAMA</th>
            <th class="text-left" width="12%">REKENING KAS</th>
            <th class="text-left" width="12%">REKENING PENDAPATAN</th>
            <th class="text-left" width="12%">REKENING PIUTANG</th>
            <th class="text-left" width="12%">REKENING DISKON</th>
            <th class="text-center">STATUS</th>
            <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($receipt_types as $val)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td>{{ $val->getDepartment->name }}</td>
            <td>{{ $val->getCategory->category }}</td>
            <td>{{ $val->name }}</td>
            <td>{{ $val->getCashAccount->code }} | {{ $val->getCashAccount->name }}</td>
            <td>{{ $val->getReceiptAccount->code }} | {{ $val->getReceiptAccount->name }}</td>
            <td>{{ $val->getReceivableAccount->code }} | {{ $val->getReceivableAccount->name }}</td>
            <td>{{ $val->getDiscountAccount->code }} | {{ $val->getDiscountAccount->name }}</td>
            <td class="text-center">{{ $reference->getActive()[$val->is_active] }}</td>
            <td>{{ $val->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>