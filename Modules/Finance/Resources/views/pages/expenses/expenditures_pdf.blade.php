<html>
  <head>
    <title>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }} - Data Transaksi Pengeluaran</title>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <div id="header">
      <div style="font-weight:bold;line-height:18px;">
        <span>{{ strtoupper(Session::get('institute')) }}</span><br/>
        <span>Data Transaksi Pengeluaran </span><br/>
        <span>Tanggal Cetak Laporan: {{ date('d/m/Y') . ' - ' . date('H:i:s') . ' WIB' }}</span><br/>
        <span>Periode: {{ $expenditures->start . ' s.d ' . $expenditures->end }}</span>
      </div>
    </div>
    <br/> 
    <table class="table" width="100%">
        <thead>
          <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Departemen</th>
            <th rowspan="2">Jurnal</th>
            <th rowspan="2" width="18%">Pemohon/Penerima</th>
            <th rowspan="2">Total</th>
            <th colspan="5" width="55%">Detil Transaksi</th>
          </tr>
          <tr>
            <th width="2%">No.</th>
            <th width="6%">Kode</th>
            <th width="20%" class="text-left">Akun</th>
            <th class="text-left">Deskripsi</th>
            <th width="10%">Jumlah</th>
          </tr>
        </thead>
        <tbody>
          @php $x = 1; @endphp
          @foreach ($expenditures->rows as $expenditure)
          <tr>
            <td class="text-center" valign="top">{{ $x }}</td>
            <td class="text-center" valign="top">{{ $expenditure->department }}</td>
            <td class="text-center" valign="top">{{ $expenditure->cash_no }}<br/>{{ $expenditure->trans_date }}</td>
            <td valign="top">
              Pemohon: {{ $expenditure->requested_name }}<br/>
              Penerima: {{ $expenditure->received_name }}
            </td>
            <td class="text-right" valign="top">{{ $expenditure->total }}</td>
            <td colspan="5">
              @php $y = 1; @endphp
              @foreach ($details as $detail)
              @if ($detail->expenditure_id == $expenditure->id)
              <table class="table no-border" width="100%">
                <tbody>
                  <tr>
                    <td valign="top" class="text-right" width="4.35%">{{ $y }}</td>
                    <td valign="top" class="text-center" width="11%">{{ $detail->code }}</td>
                    <td valign="top" width="36.2%">{{ $detail->name }}</td>
                    <td valign="top">{{ $detail->remark }}</td>
                    <td valign="top" class="text-right" width="17.7%">Rp{{ number_format($detail->credit,2) }}</td>
                  </tr>
                </tbody>
              </table>
              @php $y++; @endphp
              @endif
              @endforeach
            </td>
          </tr>
          @php $x++; @endphp
          @endforeach
        </tbody>
      </table>
  </body>
</html>