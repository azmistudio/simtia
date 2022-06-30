<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
  <head>
    <link href="file:///{{ public_path('css/print-minimal.css') }}" rel="stylesheet" />
  </head>
  <body>
    <table class="no-border">
      <tbody>
        <tr><td colspan="10" align="center" class="title"><b>{{ config('app.name') .' '. strtoupper(Session::get('institute')) }}</b></td></tr>
        <tr><td colspan="10" align="center" class="title"><b>DATA TRANSAKSI PENGELUARAN</b></td></tr>
        <tr><td colspan="10" align="center" class="subtitle"><b>PERIODE {{ $expenditures->start . ' s.d ' . $expenditures->end }}</b></td></tr>
      </tbody>
    </table>
    <br/>
    <table border="1" cellpadding="2" style="border-collapse: collapse;overflow:wrap;" width="100%">
      <thead>
        <tr style="background-color:#CCFFFF;">
          <th class="subtitle" rowspan="2">No.</th>
          <th class="subtitle" rowspan="2">Departemen</th>
          <th class="subtitle" rowspan="2">Jurnal</th>
          <th class="subtitle" rowspan="2" width="18%">Pemohon/Penerima</th>
          <th class="subtitle" rowspan="2">Total</th>
          <th class="subtitle" colspan="5" width="55%">Detil Transaksi</th>
        </tr>
        <tr style="background-color:#CCFFFF;">
          <th class="subtitle" width="2%">No.</th>
          <th class="subtitle" width="6%">Kode</th>
          <th class="subtitle" width="20%" class="text-left">Akun</th>
          <th class="subtitle" class="text-left">Deskripsi</th>
          <th class="subtitle" width="10%">Jumlah</th>
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