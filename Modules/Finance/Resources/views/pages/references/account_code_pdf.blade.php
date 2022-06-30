@inject('balances', 'Modules\Finance\Repositories\Reference\CodeEloquent')
<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Kode Akun Perkiraan</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Kode Akun Perkiraan - {{ config('app.name') }} </span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span>
        <br/> 
      </div>
    </div>
    <br/>
    <table width="100%">
      <thead>
        <tr>
            <th class="text-center">NO.</th>
            <th class="text-center">KATEGORI</th>
            <th class="text-left">KODE AKUN</th>
            <th class="text-left">NAMA AKUN</th>
            <th class="text-right">SALDO</th>
            <th class="text-left">KETERANGAN</th>
        </tr>
      </thead>
      <tbody>
        @php $num = 1; @endphp
        @foreach ($codes as $code)
          <tr>
            <td class="text-center">{{ $num }}</td>
            <td class="text-center">{{ $code->getCategory->category }}</td>
            <td>
              @if ($code->parent > 0)
              &nbsp;&nbsp;&nbsp;{{ $code->code }}
              @else
              {{ $code->code }}
              @endif
            </td>
            <td>
              @if ($code->parent > 0)
              &nbsp;&nbsp;&nbsp;{{ $code->name }}
              @else
              {{ $code->name }}
              @endif
            </td>
            <td class="text-right">
              @if ($code->parent > 0)
              Rp{{ number_format($balances->getBalanceSub($code->id, 0, date('Y-m-d')),2) }}
              @else
              <b>Rp{{ number_format($balances->getBalance($code->id, 0, date('Y-m-d')),2) }}</b>
              @endif
            </td>
            <td>{{ $code->remark }}</td>
          </tr> 
          @php $num++; @endphp
        @endforeach
      </tbody>
    </table>
  </body>
</html>